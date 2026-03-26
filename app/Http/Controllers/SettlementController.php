<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roommate;
use App\Models\Settlement;

class SettlementController extends Controller
{
    public function create(Request $request)
    {
        $roommates = Roommate::all();
        $senderId = $request->query('sender_id');
        return view('settlements.create', compact('roommates', 'senderId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|exists:roommates,id',
            'receiver_id' => 'required|exists:roommates,id|different:sender_id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        Settlement::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Settlement recorded!');
    }
}
