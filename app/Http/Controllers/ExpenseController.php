<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExchangeRate;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the user’s expenses, grouped by “Month Year”,
     * with both RM and converted totals.
     */
    public function index(ExchangeRate $fx)
    {
        $user     = auth()->user();
        $currency = $user->currency;

        // 1) Fetch base expenses
        $baseExpenses = Expense::where('user_id', $user->id)
            ->orderBy('date', 'asc')
            ->get();

        // 2) Attach a converted_amount property & return the model itself
        $withConverted = $baseExpenses->map(function (Expense $e) use ($fx, $currency) {
            $e->converted_amount = $fx->convert($e->amount, $currency);
            return $e;
        });

        // 3) Group by "Month Year"
        $expenses = $withConverted
            ->groupBy(fn(Expense $e) => Carbon::parse($e->date)->format('F Y'))
            ->sortKeys();

        // 4) Compute RM totals per month
        $driver   = DB::getDriverName();
        $monthSql = $driver === 'pgsql'
                  ? "to_char(date, 'FMMonth YYYY')"
                  : "DATE_FORMAT(date, '%M %Y')";

        $rmTotals = Expense::where('user_id', $user->id)
            ->selectRaw("$monthSql AS month_year, SUM(amount) AS total")
            ->groupBy('month_year')
            ->pluck('total', 'month_year');

        // 5) Convert those totals
        $convertedTotals = $rmTotals->map(fn($total) => $fx->convert($total, $currency));

        return view('expenses.index', [
            'expenses'         => $expenses,
            'rmTotals'         => $rmTotals,
            'convertedTotals'  => $convertedTotals,
            'currency'         => $currency,
        ]);
    }

    /**
     * Show the category breakdown for a given month,
     * with both RM and converted sums.
     */
    public function showMonthlyBreakdown(string $month, ExchangeRate $fx)
    {
        $user     = auth()->user();
        $currency = $user->currency;
    
        // Parse "May 2025" → [2025-05-01, 2025-05-31]
        $dateObj = Carbon::createFromFormat('F Y', $month);
        $start   = $dateObj->startOfMonth();
        $end     = $dateObj->endOfMonth();
    
        // 1) Raw RM totals by category
        $categoryBreakdown = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');
    
        // 2) Converted totals
        $convertedBreakdown = $categoryBreakdown
            ->map(fn($total) => $fx->convert($total, $currency));
    
            return view('expenses.breakdown', [
                'month'              => $month,
                'categoryBreakdown'  => $categoryBreakdown,
                'convertedBreakdown' => $convertedBreakdown,
                'currency'           => $currency,
                'categoryTotals'     => $categoryBreakdown, 
                'convertedTotals'    => $convertedBreakdown 
            ]);
            
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'amount'  => 'required|numeric',
            'date'    => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data           = $request->only(['title', 'amount', 'date', 'category', 'notes']);
        $data['user_id'] = auth()->id();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts', 's3');
        }

        Expense::create($data);

        return redirect()
            ->route('expenses.index')
            ->with('success','Expense & receipt saved!');
    }

    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        return view('expenses.edit', compact('expense'));
    }

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

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('s3')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts', 's3');
        }

        $expense->update($data);

        return redirect()
            ->route('expenses.index')
            ->with('success','Expense updated!');
    }

    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return redirect()
            ->route('expenses.index')
            ->with('success','Expense deleted!');
    }
}
