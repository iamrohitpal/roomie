<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Roommate;
use App\Models\Settlement;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $groupId = session('active_group_id');
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

        $expenses = $query->latest()->simplePaginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('expenses._list', compact('expenses'))->render(),
                'next_page' => $expenses->nextPageUrl(),
            ]);
        }

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
            'category' => 'nullable|string',
        ]);

        // Custom validation for splits total
        $totalSplits = array_sum($request->splits);
        if (abs($totalSplits - $request->amount) > 0.1) {
            return redirect()->back()->withErrors(['amount' => 'Sum of splits (₹'.number_format($totalSplits, 2).') must equal total amount (₹'.number_format($request->amount, 2).').'])->withInput();
        }

        $roommate = Roommate::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->first();

        if (! $roommate) {
            return redirect()->back()->with('error', 'User roommate record not found in this group.');
        }

        DB::transaction(function () use ($request, $roommate, $groupId) {
            $expense = Expense::create([
                'group_id' => $groupId,
                'description' => $request->description,
                'amount' => $request->amount,
                'payer_id' => $roommate->id,
                'date' => $request->date,
                'category' => $request->category ?? 'General',
            ]);

            foreach ($request->splits as $roommateId => $amount) {
                if ($amount > 0) {
                    ExpenseSplit::create([
                        'expense_id' => $expense->id,
                        'roommate_id' => $roommateId,
                        'amount' => $amount,
                    ]);
                }
            }
        });

        $this->firebase->notifyGroup(
            $groupId,
            'New Expense in '.auth()->user()->groups()->find($groupId)->name,
            auth()->user()->name.' added: '.$request->description.' (₹'.$request->amount.')',
            ['type' => 'expense', 'expense_id' => 123], // Dummy ID for now or get it from transaction
            auth()->id()
        );

        return redirect()->route('dashboard')->with('success', 'Expense added!');
    }

    public function edit($id)
    {
        $expense = Expense::with('splits')->findOrFail($id);

        // Authorization: Only the payer can edit
        if ($expense->payer->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You can only edit your own expenses.');
        }

        // Time check: Only within 10 minutes
        if ($expense->created_at->diffInMinutes(now()) > 10) {
            return redirect()->back()->with('error', 'You can only edit expenses within 10 minutes of adding them.');
        }

        $groupId = session('active_group_id');
        $roommates = Roommate::where('group_id', $groupId)->get();

        return view('expenses.edit', compact('expense', 'roommates'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        // Authorization & Time Check
        if ($expense->payer->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        if ($expense->created_at->diffInMinutes(now()) > 10) {
            return redirect()->back()->with('error', 'Edit window expired.');
        }

        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'splits' => 'required|array',
            'splits.*' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'nullable|string',
        ]);

        $totalSplits = array_sum($request->splits);
        if (abs($totalSplits - $request->amount) > 0.1) {
            return redirect()->back()->withErrors(['amount' => 'Sum of splits must equal total amount.'])->withInput();
        }

        DB::transaction(function () use ($request, $expense) {
            $expense->update([
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date,
                'category' => $request->category ?? 'General',
            ]);

            // Refresh splits: delete old and create new
            $expense->splits()->delete();
            foreach ($request->splits as $roommateId => $amount) {
                if ($amount > 0) {
                    ExpenseSplit::create([
                        'expense_id' => $expense->id,
                        'roommate_id' => $roommateId,
                        'amount' => $amount,
                    ]);
                }
            }
        });

        return redirect()->route('dashboard')->with('success', 'Expense updated!');
    }

    public function destroy($id)
    {
        $expense = Expense::with('group')->findOrFail($id);

        // Authorization: Group Creator Only
        $isCreator = $expense->group->created_by === auth()->id();

        if (! $isCreator) {
            return redirect()->back()->with('error', 'Only the group owner can delete expenses.');
        }

        DB::transaction(function () use ($expense) {
            $expense->splits()->delete();
            $expense->delete();
        });

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }

    public function settle(Request $request)
    {
        $groupId = session('active_group_id');

        $request->validate([
            'sender_id' => 'required|exists:roommates,id',
            'receiver_id' => 'required|exists:roommates,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        Settlement::create([
            'group_id' => $groupId,
            'sender_id' => $request->sender_id,
            'receiver_id' => $request->receiver_id,
            'amount' => $request->amount,
            'date' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Debt settled!');
    }
}
