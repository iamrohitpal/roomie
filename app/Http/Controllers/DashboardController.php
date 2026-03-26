<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roommate;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $roommates = Roommate::all();
        $recentExpenses = Expense::with('payer')->latest()->take(5)->get();
        $totalSpending = Expense::sum('amount');

        return view('dashboard', compact('roommates', 'recentExpenses', 'totalSpending'));
    }
}
