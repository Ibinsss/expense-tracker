<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy(function ($expense) {
                return Carbon::parse($expense->date)->format('F Y');
            })
            ->sortKeys();

        $totals = Expense::where('user_id', auth()->id())
            ->selectRaw("DATE_FORMAT(date, '%M %Y') as month_year, SUM(amount) as total")
            ->groupBy('month_year')
            ->pluck('total', 'month_year');

        return view('expenses.index', compact('expenses', 'totals'));
    }

    public function showMonthlyBreakdown($month)
    {
        // Convert month back to valid date format for comparison (e.g., May 2025 → 2025-05)
        $dateObj = Carbon::createFromFormat('F Y', $month);
        $start = $dateObj->startOfMonth();
        $end = $dateObj->endOfMonth();

        // Fetch expenses by category
        $categoryBreakdown = Expense::where('user_id', auth()->id())
            ->whereBetween('date', [$start, $end])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('expenses.breakdown', compact('month', 'categoryBreakdown'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required',
            'amount'      => 'required|numeric',
            'date'        => 'required|date',
            'receipt'     => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);
    
        $data = $request->only(['title','amount','date','category','notes']);
        $data['user_id'] = auth()->id();
    
        if ($request->hasFile('receipt')) {
            // store under storage/app/public/receipts:
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts','public');
        }
    
        Expense::create($data);
    
        return redirect()->route('expenses.index')
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
    
        $data = $request->only(['title','amount','date','category','notes']);
    
        if ($request->hasFile('receipt')) {
            // delete old, if any
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts','public');
        }
    
        $expense->update($data);
    
        return redirect()->route('expenses.index')
                         ->with('success','Expense updated!');
    }
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted!');
    }
}
