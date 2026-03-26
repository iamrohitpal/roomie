<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use App\Models\Roommate;
use App\Models\ExpenseSplit;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('payer')->latest()->get();
        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $roommates = Roommate::all();
        return view('expenses.create', compact('roommates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'splits' => 'required|array',
            'splits.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string'
        ]);

        $roommate = \App\Models\Roommate::where('user_id', auth()->id())->first();
        
        if (!$roommate) {
             return redirect()->back()->with('error', 'User roommate record not found.');
        }

        DB::transaction(function () use ($request, $roommate) {
            $expense = \App\Models\Expense::create([
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

        return redirect()->route('dashboard')->with('success', 'Expense added!');
    }
}
