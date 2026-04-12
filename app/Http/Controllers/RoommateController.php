<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Roommate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoommateController extends Controller
{
    public function index()
    {
        $groupId = session('active_group_id');
        $roommates = Roommate::where('group_id', $groupId)->get();

        // Only show users who are members of this group but don't have a roommate record yet (edge case)
        $groupUserIds = Group::find($groupId)->users->pluck('id');
        $existingRoommateUserIds = $roommates->pluck('user_id')->filter()->toArray();
        $otherUsersInGroup = User::whereIn('id', $groupUserIds)->whereNotIn('id', $existingRoommateUserIds)->get();

        return view('roommates.index', compact('roommates', 'otherUsersInGroup'));
    }

    public function addFromUser(User $user)
    {
        $groupId = session('active_group_id');

        Roommate::firstOrCreate(
            ['user_id' => $user->id, 'group_id' => $groupId],
            [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
            ]
        );

        return redirect()->route('roommates.index')->with('success', $user->name.' added to roommate list!');
    }

    public function store(Request $request)
    {
        $groupId = session('active_group_id');

        $request->validate([
            'name' => 'required|string|max:50|min:2',
            'phone' => [
                'required', 'numeric', 'digits:10',
                Rule::unique('roommates')->where(fn ($query) => $query->where('group_id', $groupId))
            ],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('roommates')->where(fn ($query) => $query->where('group_id', $groupId))
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['group_id'] = $groupId;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/'.$path);
        }

        Roommate::create($data);

        return redirect()->route('roommates.index')->with('success', 'Roommate added successfully!');
    }

    public function destroy(Roommate $roommate)
    {
        $groupId = session('active_group_id');

        // Ensure roommate belongs to the active group
        if ($roommate->group_id != $groupId) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // If this roommate is linked to a user, we DON'T remove the user from the group's total members (user_groups table)
        // unless they are explicitly leaving the group. This just removes the roommate record (the ledger entry).
        // However, if the user wants to leave the group, that's a different flow.
        // For now, removing the roommate just removes them from the expense splitting lists.

        $roommate->delete();

        return redirect()->route('roommates.index')->with('success', 'Roommate removed from the group splitting list.');
    }
}
