<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Roommate;

use App\Models\User;

class RoommateController extends Controller
{
    public function index()
    {
        $roommates = Roommate::all();
        $existingUserIds = $roommates->pluck('user_id')->filter()->toArray();
        $otherUsers = User::whereNotIn('id', $existingUserIds)->get();

        return view('roommates.index', compact('roommates', 'otherUsers'));
    }

    public function addFromUser(User $user)
    {
        Roommate::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ]
        );

        return redirect()->route('roommates.index')->with('success', $user->name . ' added to group!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:15',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        Roommate::create($data);

        return redirect()->route('roommates.index')->with('success', 'Roommate added successfully!');
    }
}
