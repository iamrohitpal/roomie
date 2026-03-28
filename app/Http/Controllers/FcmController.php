<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmController extends Controller
{
    /**
     * Update the user's FCM token.
     */
    public function updateToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->fcm_token = $request->token;
            $user->save();

            return response()->json(['message' => 'Token updated successfully']);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }

    /**
     * Remove the user's FCM token.
     */
    public function deleteToken(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->fcm_token = null;
            $user->save();

            return response()->json(['message' => 'Notifications disabled successfully']);
        }

        return response()->json(['message' => 'User not authenticated'], 401);
    }
}
