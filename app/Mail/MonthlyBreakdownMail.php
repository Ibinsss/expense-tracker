<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlyBreakdownMail extends Mailable
{
    use Queueable, SerializesModels;

    public $month, $categoryTotals, $userName;

    public function __construct($month, $categoryTotals, $userName)
    {
        $this->month = $month;
        $this->categoryTotals = $categoryTotals;
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Your Expense Breakdown for ' . $this->month)
                    ->view('emails.breakdown')
                    ->with([
                        'month' => $this->month,
                        'categoryTotals' => $this->categoryTotals,
                        'name' => $this->userName, // <- fix here
                    ]);
    }
    
    
}
