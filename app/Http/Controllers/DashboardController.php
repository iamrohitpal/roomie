<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Roommate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $groupId = session('active_group_id');
        $roommates = Roommate::where('group_id', $groupId)->get();
        $query = Expense::where('group_id', $groupId)->with('payer');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereHas('payer', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $recentExpenses = $query->latest()->simplePaginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('expenses._recent', compact('recentExpenses'))->render(),
                'next_page' => $recentExpenses->nextPageUrl(),
            ]);
        }

        $totalSpending = Expense::where('group_id', $groupId)->sum('amount');

        // Find current user's roommate record in this group
        $currentUserRoommate = Roommate::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->first();

        $myBalance = 0;
        if ($currentUserRoommate) {
            $paid = Expense::where('group_id', $groupId)->where('payer_id', $currentUserRoommate->id)->sum('amount');
            $borrowed = ExpenseSplit::whereHas('expense', function ($q) use ($groupId) {
                $q->where('group_id', $groupId);
            })->where('roommate_id', $currentUserRoommate->id)->sum('amount');

            $myBalance = $paid - $borrowed;
        }

        return view('dashboard', compact('roommates', 'recentExpenses', 'totalSpending', 'myBalance'));
    }
}
