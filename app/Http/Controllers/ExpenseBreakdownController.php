<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExpenseBreakdownController extends Controller
{
    /* ---------------------------------------------------------
     |  Create
     * --------------------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'amount'  => 'required|numeric',
            'date'    => 'required|date',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:5120',
        ]);

        $data            = $request->only(['title','amount','date','category','notes']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')
                                           ->store('receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('expenses.index')
                         ->with('success', 'Expense added!');
    }

    /* ---------------------------------------------------------
     |  Update
     * --------------------------------------------------------*/
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
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')
                                           ->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('expenses.index')
                         ->with('success','Expense updated!');
    }

    /* ---------------------------------------------------------
     |  A.  Browser breakdown page  (/expenses/breakdown/{month})
     * --------------------------------------------------------*/
    public function show(string $month)
    {
        [$start, $end] = $this->monthBounds($month);

        $expenses = Expense::where('user_id', Auth::id())
                           ->whereBetween('date', [$start, $end])
                           ->get();

        $categoryTotals = $expenses->groupBy('category')
                                   ->map->sum('amount');

        return view('expenses.breakdown', [
            'month'          => $month,
            'categoryTotals' => $categoryTotals,
        ]);
    }

    /* ---------------------------------------------------------
     |  B.  SendGrid e‑mail POST  (/expenses/breakdown/{month}/email)
     * --------------------------------------------------------*/
    public function email(Request $request, string $month)
    {
        [$start, $end] = $this->monthBounds($month);

        $user           = Auth::user();
        $recipientEmail = $request->input('email', $user->email);

        $expenses = Expense::where('user_id', $user->id)
                           ->whereBetween('date', [$start, $end])
                           ->get();

        $categoryTotals = $expenses->groupBy('category')
                                   ->map->sum('amount');

        /* ------------ Render Blade to HTML -------------------*/
        $html = view('emails.breakdown', [
                    'name'           => $user->name,
                    'month'          => $month,
                    'categoryTotals' => $categoryTotals,
                 ])->render();

        /* ------------ Call SendGrid --------------------------*/
        $resp = Http::withHeaders([
                    'Authorization' => 'Bearer '.config('services.sendgrid.key'),
                    'Content-Type'  => 'application/json',
                ])->post('https://api.sendgrid.com/v3/mail/send', [
                    'personalizations' => [[
                        'to'      => [['email' => $recipientEmail]],
                        'subject' => "Your Expense Breakdown for {$month}",
                    ]],
                    'from'    => ['email' => 'no-reply@expensetracker.com',
                                  'name'  => 'Expense Tracker'],
                    'content' => [[ 'type' => 'text/html', 'value' => $html ]],
                ]);

        if ($resp->successful()) {
            return back()->with('success',"Breakdown sent to {$recipientEmail}");
        }

        Log::error('SendGrid error', ['status'=>$resp->status(), 'body'=>$resp->body()]);
        return back()->with('error','Failed to send email – check logs.');
    }

    /* ---------------------------------------------------------
     |  Helper: get first & last day for “May 2025”
     * --------------------------------------------------------*/
    private function monthBounds(string $month): array
    {
        $date   = Carbon::createFromFormat('F Y', $month);
        return [
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString(),
        ];
    }
}
