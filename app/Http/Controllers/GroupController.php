<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Expense;
use App\Models\ExpenseSplit;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseService;

class GroupController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }
    public function index()
    {
        $groups = Auth::user()->groups;
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $group = Group::create([
            'name' => $request->name,
            'invite_code' => strtoupper(Str::random(6)),
            'created_by' => Auth::id(),
        ]);

        $group->users()->attach(Auth::id(), ['role' => 'admin']);
        
        // Auto-create a roommate entry for the creator in this group
        \App\Models\Roommate::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'phone' => Auth::user()->phone,
            'avatar' => Auth::user()->avatar,
        ]);

        session(['active_group_id' => $group->id]);

        return redirect()->route('dashboard')->with('success', "Group '{$group->name}' created!");
    }

    public function join(Request $request)
    {
        return view('groups.join', ['invite_code' => $request->code]);
    }

    public function joinProcess(Request $request)
    {
        $request->validate(['invite_code' => 'required|string']);

        $group = Group::where('invite_code', $request->invite_code)->first();

        if (!$group) {
            return back()->withErrors(['invite_code' => 'Invalid invite code.']);
        }

        if ($group->users()->where('user_id', Auth::id())->exists()) {
            session(['active_group_id' => $group->id]);
            return redirect()->route('dashboard')->with('info', "You are already a member of {$group->name}.");
        }

        $group->users()->attach(Auth::id(), ['role' => 'member']);
        
        // Create roommate if doesn't exist
        $roommate = \App\Models\Roommate::firstOrCreate(
            ['group_id' => $group->id, 'user_id' => Auth::id()],
            ['name' => Auth::user()->name, 'phone' => Auth::user()->phone ?? '']
        );

        session(['active_group_id' => $group->id]);

        // Notify existing members
        $this->firebase->notifyGroup(
            $group->id,
            "New Roommate Joined!",
            Auth::user()->name . " has joined " . $group->name . ".",
            ['type' => 'new_member'],
            Auth::id()
        );

        return redirect()->route('dashboard')->with('success', 'Joined group successfully!');
    }

    public function switch($id)
    {
        $group = Auth::user()->groups()->findOrFail($id);
        session(['active_group_id' => $group->id]);
        return redirect()->route('dashboard');
    }

    public function clearData(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id'
        ]);

        $groupId = $request->group_id;

        // Verify user belongs to this group
        if (!auth()->user()->groups()->where('groups.id', $groupId)->exists()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Delete all expenses and their splits in bulk
        ExpenseSplit::whereIn('expense_id', Expense::where('group_id', $groupId)->pluck('id'))->delete();
        Expense::where('group_id', $groupId)->delete();

        // Delete all settlements
        \App\Models\Settlement::where('group_id', $groupId)->delete();

        return redirect()->back()->with('success', 'All group data has been cleared successfully.');
    }

    public function exportCsv($id)
    {
        $group = auth()->user()->groups()->findOrFail($id);
        $filename = "Roomie_Report_" . Str::slug($group->name) . "_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($group) {
            $file = fopen('php://output', 'w');

            // Group Info
            fputcsv($file, ["GROUP REPORT: " . $group->name]);
            fputcsv($file, ["Generated on: " . date('Y-m-d H:i:s')]);
            fputcsv($file, []);

            // Expenses Section
            fputcsv($file, ["--- EXPENSES ---"]);
            fputcsv($file, ["Date", "Description", "Payer", "Category", "Amount (INR)", "Splits"]);
            $expenses = Expense::where('group_id', $group->id)->with(['payer', 'splits.roommate'])->latest()->get();
            foreach ($expenses as $e) {
                $splitInfo = $e->splits->map(fn($s) => $s->roommate->name . ": " . $s->amount)->join('; ');
                fputcsv($file, [$e->date, $e->description, $e->payer->name, $e->category, $e->amount, $splitInfo]);
            }
            fputcsv($file, []);

            // Settlements Section
            fputcsv($file, ["--- SETTLEMENTS ---"]);
            fputcsv($file, ["Date", "From", "To", "Amount (INR)"]);
            $settlements = Settlement::where('group_id', $group->id)->with(['sender', 'receiver'])->latest()->get();
            foreach ($settlements as $s) {
                fputcsv($file, [$s->date, $s->sender->name, $s->receiver->name, $s->amount]);
            }
            fputcsv($file, []);

            // Summary Section
            fputcsv($file, ["--- USER SUMMARY ---"]);
            fputcsv($file, ["Roommate", "Phone", "Total Paid", "Total Borrowed", "Net Balance"]);
            $roommates = \App\Models\Roommate::where('group_id', $group->id)->get();
            foreach ($roommates as $r) {
                $paid = Expense::where('payer_id', $r->id)->sum('amount');
                $borrowed = ExpenseSplit::where('roommate_id', $r->id)->sum('amount');
                fputcsv($file, [$r->name, $r->phone, $paid, $borrowed, $paid - $borrowed]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
