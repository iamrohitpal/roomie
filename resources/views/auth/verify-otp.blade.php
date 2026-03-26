@extends('layout')

@section('content')
    <div class="animate-fade-in" style="margin-top: 60px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div class="logo" style="font-size: 2rem;">Verification</div>
            <p style="color: var(--text-dim); margin-top: 8px;">We sent a 6-digit code to +91 {{ session('auth_phone') }}</p>
        </div>

        @if (session('debug_otp'))
            <div class="card"
                style="border-color: var(--secondary); background: rgba(14, 165, 233, 0.1); margin-bottom: 20px;">
                <p style="color: var(--secondary); font-size: 0.8125rem; font-weight: 600; text-align: center;">
                    DEBUG: Your OTP is {{ session('debug_otp') }}
                </p>
            </div>
        @endif

        <div class="card">
            <form action="{{ url('/verify-otp') }}" method="POST">
                @csrf
                <div style="margin-bottom: 24px;">
                    <label
                        style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 12px; text-align: center;">Enter
                        OTP Code</label>
                    <input type="text" name="otp" maxlength="6" placeholder="000000" required autofocus
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 12px; padding: 16px; color: white; outline: none; font-size: 1.5rem; text-align: center; letter-spacing: 0.5em; font-weight: 700;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px;">Verify &
                    Continue</button>
            </form>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ route('login') }}"
                    style="color: var(--text-dim); text-decoration: none; font-size: 0.8125rem;">Wrong Number?</a>
            </div>
        </div>
    </div>

    <style>
        .bottom-nav,
        .fab {
            display: none !important;
        }

        body {
            padding-bottom: 20px;
        }
    </style>
@endsection
