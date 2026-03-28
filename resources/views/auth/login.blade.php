@extends('layout')

@section('content')
    <div class="animate-fade-in" style="margin-top: 60px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div class="logo" style="font-size: 2.5rem;">Roomie</div>
            <p style="color: var(--text-dim); margin-top: 8px;">Split expenses with ease.</p>
        </div>

        <div class="card">
            <h2 style="font-size: 1.25rem; margin-bottom: 8px;">Welcome</h2>
            <p style="font-size: 0.875rem; color: var(--text-dim); margin-bottom: 24px;">Enter your phone number to continue.
            </p>

            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 8px;">Phone
                        Number</label>
                    <div
                        style="display: flex; gap: 12px; align-items: center; background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $errors->has('phone') ? 'var(--accent)' : 'var(--glass-border)' }}; border-radius: 12px; padding: 4px 16px;">
                        <span style="color: var(--text-dim); font-weight: 600;">+91</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="9876543210" required
                            style="flex: 1; background: transparent; border: none; padding: 12px 0; color: white; outline: none; font-size: 1rem; font-weight: 500; letter-spacing: 0.1em;">
                    </div>
                    @error('phone')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 8px;"><i
                                class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 16px;">Send OTP</button>
            </form>
        </div>

        <p style="text-align: center; font-size: 0.75rem; color: var(--text-dim); margin-top: 24px;">
            By continuing, you agree to our Terms and Privacy Policy.
        </p>
    </div>

    <style>
        /* Hide bottom nav on login page */
        .bottom-nav,
        .fab {
            display: none !important;
        }

        body {
            padding-bottom: 20px;
        }
    </style>
@endsection
