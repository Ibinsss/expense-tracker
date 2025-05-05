<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $expenses = Expense::where('user_id', auth()->id())->latest()->take(5)->get(); // latest 5
        return view('dashboard', compact('expenses'));
    }
}
