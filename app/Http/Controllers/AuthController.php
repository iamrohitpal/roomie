<?php

namespace App\Http\Controllers;

use App\Models\Roommate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits:10',
        ]);

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(10);

        $user = User::firstOrCreate(['phone' => $request->phone]);
        $user->update([
            'otp' => $otp, // In production, hash this or use a secure service
            'otp_expires_at' => $expiresAt,
        ]);

        // SIMULATED: Send SMS here
        session(['auth_phone' => $request->phone, 'debug_otp' => $otp]);

        return redirect()->route('auth.verify-otp')->with('info', 'OTP sent to your phone!');
    }

    public function showVerifyOtp()
    {
        if (! session('auth_phone')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $phone = session('auth_phone');
        $user = User::where('phone', $phone)->first();

        if (! $user || $user->otp !== $request->otp || Carbon::now()->gt($user->otp_expires_at)) {
            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Clear OTP
        $user->update(['otp' => null, 'otp_expires_at' => null]);

        // Sync memberships and info BEFORE login/check
        $this->syncGroupMemberships($user);

        Auth::login($user, true);

        if (empty($user->name)) {
            return redirect()->route('auth.profile-setup');
        }

        return redirect()->route('dashboard');
    }

    public function showProfileSetup()
    {
        if (! session('auth_phone') && ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $phone = $user ? $user->phone : session('auth_phone');

        // Note: verifyOtp now handles the sync, so Auth::user()->name should already be set if found
        $prefilledName = $user ? $user->name : '';
        $prefilledAvatar = $user ? $user->avatar : '';

        // Fallback for safety or session-only flow
        if (empty($prefilledName) || empty($prefilledAvatar)) {
            $roommate = Roommate::where('phone', $phone)->first();
            if ($roommate) {
                if (empty($prefilledName)) $prefilledName = $roommate->name;
                if (empty($prefilledAvatar)) $prefilledAvatar = $roommate->avatar;
            }
        }

        return view('auth.profile-setup', compact('prefilledName', 'prefilledAvatar'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|min:2',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $phone = session('auth_phone');
        $user = $phone ? User::where('phone', $phone)->first() : Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $data = ['name' => $request->name];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/'.$path);
        }

        $user->update($data);

        $this->syncGroupMemberships($user);

        Auth::login($user, true);
        session()->forget(['auth_phone', 'debug_otp']);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    private function syncGroupMemberships(User $user)
    {
        // Find all roommate records matching this phone
        $roommates = Roommate::where('phone', $user->phone)->get();

        foreach ($roommates as $roommate) {
            // If user's info is missing, take it from the roommate record
            if (empty($user->name) && ! empty($roommate->name)) {
                $user->update([
                    'name' => $roommate->name,
                    'avatar' => $user->avatar ?? $roommate->avatar,
                ]);
            }

            // Link existing roommate record to this user
            $roommate->update([
                'user_id' => $user->id,
                'name' => $user->name ?? $roommate->name,
                'avatar' => $user->avatar ?? $roommate->avatar,
                'email' => $user->email ?? $roommate->email,
            ]);

            // Automatically join the group if not already a member
            if (! $user->groups()->where('group_id', $roommate->group_id)->exists()) {
                $user->groups()->attach($roommate->group_id, ['role' => 'member']);
            }
        }
    }
}
