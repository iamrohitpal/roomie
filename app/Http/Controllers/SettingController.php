<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $groups = auth()->user()->groups;
        return view('settings.index', compact('groups'));
    }
}
