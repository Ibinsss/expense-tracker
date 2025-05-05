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

    /* ---------------------------------------------------------------------
     | 1. INDEX  (list all months)
     * -------------------------------------------------------------------*/
    public function index()
    {
        $userId   = auth()->id();
        $cacheKey = "expenses_user_{$userId}";

        /* 1‑a  collection (per‑month list) --------------------------------*/
        $expenses = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId) {
            return Expense::where('user_id', $userId)
                ->orderBy('date', 'asc')
                ->get()
                ->groupBy(fn ($e) => Carbon::parse($e->date)->format('F Y'))
                ->sortKeys();
        });

        /* 1‑b  per‑month totals  (driver‑aware!) --------------------------*/
        $driver      = DB::getDriverName();   // "mysql" | "pgsql"
        $monthSql    = $driver === 'pgsql'
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

    /* ---------------------------------------------------------------------
     | 3.  CREATE / STORE / UPDATE / DESTROY
     |     (all your original code – unchanged)
     * -------------------------------------------------------------------*/
    // … everything else in the controller is identical …

}
