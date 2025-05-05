<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class ExpenseBreakdownController extends Controller
{

        public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required',
            'amount'    => 'required|numeric',
            'date'      => 'required|date',
            'receipt'   => 'nullable|image|max:2048', // JPG/PNG/GIF max 2MB
        ]);

        $data = $request->only(['title','amount','date','category','notes']);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request
                ->file('receipt')
                ->store('receipts','public');
        }

        $data['user_id'] = auth()->id();

        Expense::create($data);

        return redirect()->route('expenses.index')
                        ->with('success','Expense added!');
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);

        $request->validate([
            'title'     => 'required',
            'amount'    => 'required|numeric',
            'date'      => 'required|date',
            'receipt'   => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['title','amount','date','category','notes']);

        if ($request->hasFile('receipt')) {
            // delete old if exists
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

    /* -------------------------------------------------------------
     |  Show the per-month breakdown page
     * ------------------------------------------------------------ */
    public function show(string $month)
    {
        $userId = Auth::id();

        $expenses = Expense::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(date, '%M %Y') = ?", [$month])
            ->get();

        // totals per category
        $categoryTotals = $expenses
            ->groupBy('category')
            ->map->sum('amount');

        return view('expenses.breakdown', [
            'month'          => $month,
            'categoryTotals' => $categoryTotals,
        ]);
    }

    /* -------------------------------------------------------------
     |  Send the breakdown email via SendGrid API
     * ------------------------------------------------------------ */
    public function email(Request $request, string $month)
    {
        $user           = Auth::user();
        $recipientEmail = $request->input('email', $user->email);

        // rebuild the same category totals
        $expenses = Expense::where('user_id', $user->id)
            ->whereRaw("DATE_FORMAT(date, '%M %Y') = ?", [$month])
            ->get();

        $categoryTotals = $expenses
            ->groupBy('category')
            ->map->sum('amount');

        // Render Blade → HTML
        $htmlContent = view('emails.breakdown', [
            'month'          => $month,
            'categoryTotals' => $categoryTotals,
            'name'           => $user->name,
        ])->render();

        /* ---- SendGrid REST call -------------------------------- */
        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.sendgrid.key'),
                'Content-Type'  => 'application/json',
            ])
            ->post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [[
                    'to'      => [['email' => $recipientEmail]],
                    'subject' => "Your Expense Breakdown for {$month}",
                ]],
                'from' => [
                    'email' => 'no-reply@expensetracker.com',
                    'name'  => 'Expense Tracker',
                ],
                'content' => [[
                    'type'  => 'text/html',
                    'value' => $htmlContent,
                ]],
            ]);

        /* ---- Handle result ------------------------------------- */
        if ($response->successful()) {
            return back()->with('success', "Expense breakdown sent to {$recipientEmail}");
        }

        // log for debugging
        Log::info('SendGrid response', [
            'to'     => $recipientEmail,
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        
        if ($response->successful()) {
            return back()->with('success', "Expense breakdown sent to {$recipientEmail}");
        }
        
        // Only reach here on failure
        Log::error('SendGrid API error', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        
        return back()->with(
            'error',
            "Failed to send email (SendGrid status {$response->status()}). Check the log for details."
        );

        return back()->with(
            'error',
            'Failed to send email (SendGrid status '
            . $response->status() . '). Check the log for details.'
        );
    }
}
