<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    /* ---------------------------------------------------------------------
     | 1. INDEX  (list all months)
     * -------------------------------------------------------------------*/
    public function index()
    {
        $userId   = auth()->id();
        $cacheKey = "expenses_user_{$userId}";

        /* 1-a  collection (per-month list) -------------------------------*/
        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId) {
            return Expense::where('user_id', $userId)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(fn ($e) => Carbon::parse($e->date)->format('F Y'))
                ->sortKeys();
        });

        /* 1-b  per-month totals  (driver-aware!) --------------------------*/
        $driver   = DB::getDriverName();   // "mysql" | "pgsql"
        $monthSql = $driver === 'pgsql'
                  ? "to_char(date, 'FMMonth YYYY')"
                  : "DATE_FORMAT(date, '%M %Y')";

        $totals = Expense::where('user_id', $userId)
            ->selectRaw("$monthSql AS month_year, SUM(amount) AS total")
            ->groupBy('month_year')
            ->pluck('total', 'month_year');

        return view('expenses.index', compact('expenses', 'totals'));
    }

    /* ---------------------------------------------------------------------
     | 2. MONTHLY BREAKDOWN  (unchanged)
     * -------------------------------------------------------------------*/
    public function showMonthlyBreakdown($month)
    {
        $userId   = auth()->id();
        $cacheKey = "breakdown_{$userId}_" . str_replace(' ', '_', $month);

        [$categoryBreakdown] = Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($userId, $month) {
                $d    = Carbon::createFromFormat('F Y', $month);
                $from = $d->startOfMonth();
                $to   = $d->endOfMonth();

                return [
                    Expense::where('user_id', $userId)
                        ->whereBetween('date', [$from, $to])
                        ->select('category', DB::raw('SUM(amount) as total'))
                        ->groupBy('category')
                        ->pluck('total', 'category'),
                ];
            }
        );

        return view('expenses.breakdown', [
            'month'             => $month,
            'categoryBreakdown' => $categoryBreakdown,
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
     * Reads uploaded receipt into BLOB fields.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'amount'  => 'required|numeric',
            'date'    => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data = $request->only(['title', 'amount', 'date', 'category', 'notes']);
        $data['user_id'] = auth()->id();

        if ($file = $request->file('receipt')) {
            $data['receipt_data'] = file_get_contents($file->getRealPath());
            $data['receipt_mime'] = $file->getClientMimeType();
        }

        $expense = Expense::create($data);

        Log::info('Expense created', [
            'user_id'    => auth()->id(),
            'expense_id' => $expense->id,
        ]);

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
     * If a new receipt is uploaded, overwrite the BLOB fields.
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $request->validate([
            'title'   => 'required',
            'amount'  => 'required|numeric',
            'date'    => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data = $request->only(['title', 'amount', 'date', 'category', 'notes']);

        if ($file = $request->file('receipt')) {
            $data['receipt_data'] = file_get_contents($file->getRealPath());
            $data['receipt_mime'] = $file->getClientMimeType();
        }

        $expense->update($data);

        Log::info('Expense updated', [
            'user_id'    => auth()->id(),
            'expense_id' => $expense->id,
        ]);

        Cache::forget("expenses_user_".auth()->id());

        return redirect()->route('expenses.index')
                         ->with('success','Expense updated!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        Log::warning('Expense deleted', [
            'user_id'    => auth()->id(),
            'expense_id' => $expense->id,
        ]);

        Cache::forget("expenses_user_".auth()->id());

        return redirect()->route('expenses.index')
                         ->with('success','Expense deleted!');
    }
}
