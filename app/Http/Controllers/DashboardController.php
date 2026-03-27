<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roommate;
use App\Models\Expense;

class DashboardController extends Controller
{
    public function index()
    {
        $groupId = session('active_group_id');
        $roommates = Roommate::where('group_id', $groupId)->get();
        $recentExpenses = Expense::where('group_id', $groupId)->with('payer')->latest()->take(5)->get();
        
        $totalSpending = Expense::where('group_id', $groupId)->sum('amount');
        
        // Find current user's roommate record in this group
        $currentUserRoommate = Roommate::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->first();
            
        $myBalance = 0;
        if ($currentUserRoommate) {
            $paid = Expense::where('group_id', $groupId)->where('payer_id', $currentUserRoommate->id)->sum('amount');
            $borrowed = \App\Models\ExpenseSplit::whereHas('expense', function($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })->where('roommate_id', $currentUserRoommate->id)->sum('amount');
            
            $myBalance = $paid - $borrowed;
        }

        return view('dashboard', compact('roommates', 'recentExpenses', 'totalSpending', 'myBalance'));
    }
}
