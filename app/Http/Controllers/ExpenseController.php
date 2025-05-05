<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the user's expenses, grouped by month.
     * Cached per user for 5 minutes.
     */
    public function index()
    {
        $userId   = auth()->id();
        $cacheKey = "expenses_user_{$userId}";

        // Cache the grouped collection for 5 minutes
        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId) {
            return Expense::where('user_id', $userId)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(fn($expense) => Carbon::parse($expense->date)->format('F Y'))
                ->sortKeys();
        });

        $totals = Expense::where('user_id', $userId)
            ->selectRaw("DATE_FORMAT(date, '%M %Y') as month_year, SUM(amount) as total")
            ->groupBy('month_year')
            ->pluck('total', 'month_year');

        return view('expenses.index', compact('expenses', 'totals'));
    }

    /**
     * Show the per-month breakdown page.
     * Cached per user-month for 5 minutes.
     */
    public function showMonthlyBreakdown($month)
    {
        $userId   = auth()->id();
        $cacheKey = "breakdown_{$userId}_" . str_replace(' ', '_', $month);

        [$categoryBreakdown, /*not used here but you can also cache totals*/] =
            Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId, $month) {
                $dateObj = Carbon::createFromFormat('F Y', $month);
                $start   = $dateObj->startOfMonth()->toDateString();
                $end     = $dateObj->endOfMonth()->toDateString();

                $breakdown = Expense::where('user_id', $userId)
                    ->whereBetween('date', [$start, $end])
                    ->select('category', DB::raw('SUM(amount) as total'))
                    ->groupBy('category')
                    ->pluck('total', 'category');

                return [$breakdown];
            });

        return view('expenses.breakdown', [
            'month'              => $month,
            'categoryBreakdown'  => $categoryBreakdown,
        ]);
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created expense in storage.
     * Logs creation and clears index cache.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required',
            'amount'     => 'required|numeric',
            'date'       => 'required|date',
            'receipt'    => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data = $request->only(['title','amount','date','category','notes']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('receipt')) {
            // store on whatever disk is default (public locally, s3 on Heroku)
            $path = $request->file('receipt')
                            ->store('receipts', config('filesystems.default'));
            $data['receipt_path'] = $path;
        }

        $expense = Expense::create($data);

        // Logging
        Log::info('Expense created', [
            'user_id'    => auth()->id(),
            'expense_id' => $expense->id,
            'data'       => $expense->toArray(),
        ]);

        // Clear cache so new item shows up
        Cache::forget("expenses_user_".auth()->id());

        return redirect()->route('expenses.index')
                         ->with('success','Expense & receipt saved!');
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        return view('expenses.edit', compact('expense'));
    }

    /**
     * Update the specified expense in storage.
     * Logs update and clears index cache.
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $request->validate([
            'title'      => 'required',
            'amount'     => 'required|numeric',
            'date'       => 'required|date',
            'receipt'    => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $original = $expense->getOriginal();

        $data = $request->only(['title','amount','date','category','notes']);

        if ($request->hasFile('receipt')) {
            // delete old
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts','public');
        }

        $expense->update($data);

        // Logging
        Log::info('Expense updated', [
            'user_id'     => auth()->id(),
            'expense_id'  => $expense->id,
            'before'      => $original,
            'after'       => $expense->getChanges(),
        ]);

        // Clear cache
        Cache::forget("expenses_user_".auth()->id());

        return redirect()->route('expenses.index')
                         ->with('success','Expense updated!');
    }

    /**
     * Remove the specified expense from storage.
     * Logs deletion and clears index cache.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        // Logging
        Log::warning('Expense deleted', [
            'user_id'    => auth()->id(),
            'expense_id' => $expense->id,
            'data'       => $expense->toArray(),
        ]);

        $expense->delete();

        // Clear cache
        Cache::forget("expenses_user_".auth()->id());

        return redirect()->route('expenses.index')
                         ->with('success','Expense deleted!');
    }
}
