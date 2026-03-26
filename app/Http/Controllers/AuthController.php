<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
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
        if (!session('auth_phone')) return redirect()->route('login');
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $phone = session('auth_phone');
        $user = User::where('phone', $phone)->first();

        if (!$user || $user->otp !== $request->otp || Carbon::now()->gt($user->otp_expires_at)) {
            return redirect()->back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        // Clear OTP
        $user->update(['otp' => null, 'otp_expires_at' => null]);

        Auth::login($user, true);

        if (empty($user->name)) {
            return redirect()->route('auth.profile-setup');
        }

        return redirect()->route('dashboard');
    }

    public function showProfileSetup()
    {
        if (!session('auth_phone') && !Auth::check()) return redirect()->route('login');
        return view('auth.profile-setup');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $phone = session('auth_phone');
        $user = $phone ? User::where('phone', $phone)->first() : Auth::user();

        if (!$user) return redirect()->route('login');

        $data = ['name' => $request->name];

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = asset('storage/' . $path);
        }

        $user->update($data);

        // Sync with Roommate record
        // First check if a roommate exists with this phone but no user_id
        $existingRoommate = \App\Models\Roommate::where('phone', $user->phone)
            ->whereNull('user_id')
            ->first();

        if ($existingRoommate) {
            $existingRoommate->update([
                'user_id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'email' => $user->email ?? $existingRoommate->email,
            ]);
        } else {
            \App\Models\Roommate::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone' => $user->phone,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ]
            );
        }

        Auth::login($user, true);
        session()->forget(['auth_phone', 'debug_otp']);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
