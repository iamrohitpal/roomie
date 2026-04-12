<?php

namespace App\Http\Controllers;

class SettingController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $allGroups = $user->groups;
        $ownedGroups = \App\Models\Group::where('created_by', $user->id)->get();

        return view('settings.index', compact('allGroups', 'ownedGroups'));
    }
}
