<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Roommate;
use App\Models\ExpenseSplit;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseService;

class ExpenseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    public function index()
    {
        $groupId = session('active_group_id');
        $expenses = Expense::where('group_id', $groupId)->with('payer')->latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $groupId = session('active_group_id');
        $roommates = Roommate::where('group_id', $groupId)->get();
        return view('expenses.create', compact('roommates'));
    }

    public function store(Request $request)
    {
        $groupId = session('active_group_id');
        
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'splits' => 'required|array',
            'splits.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string'
        ]);

        $roommate = \App\Models\Roommate::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->first();
        
        if (!$roommate) {
             return redirect()->back()->with('error', 'User roommate record not found in this group.');
        }

        DB::transaction(function () use ($request, $roommate, $groupId) {
            $expense = \App\Models\Expense::create([
                'group_id' => $groupId,
                'description' => $request->description,
                'amount' => $request->amount,
                'payer_id' => $roommate->id,
                'date' => $request->date,
                'category' => $request->category ?? 'General'
            ]);

            foreach ($request->splits as $roommateId => $amount) {
                if ($amount > 0) {
                    \App\Models\ExpenseSplit::create([
                        'expense_id' => $expense->id,
                        'roommate_id' => $roommateId,
                        'amount' => $amount,
                    ]);
                }
            }
        });

        $this->firebase->notifyGroup(
            $groupId, 
            "New Expense in " . auth()->user()->groups()->find($groupId)->name,
            auth()->user()->name . " added: " . $request->description . " (₹" . $request->amount . ")",
            ['type' => 'expense', 'expense_id' => 123], // Dummy ID for now or get it from transaction
            auth()->id()
        );

        return redirect()->route('dashboard')->with('success', 'Expense added!');
    }
    public function settle(Request $request)
    {
        $groupId = session('active_group_id');
        
        $request->validate([
            'sender_id' => 'required|exists:roommates,id',
            'receiver_id' => 'required|exists:roommates,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        \App\Models\Settlement::create([
            'group_id' => $groupId,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'amount' => $request->amount,
            'date' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Debt settled!');
    }
}
