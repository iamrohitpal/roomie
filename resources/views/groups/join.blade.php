@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <a href="{{ route('groups.index') }}"
                style="display: inline-flex; align-items: center; color: var(--primary-light); text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i>
                Back to Groups
            </a>
            <a href="{{ route('groups.create') }}"
                style="display: inline-flex; align-items: center; color: var(--primary-light); text-decoration: none; font-size: 0.875rem; font-weight: 600;">
                Create Group <i class="fa-solid fa-plus-circle" style="margin-left: 8px;"></i>
            </a>
        </div>

        <div class="card">
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">Join Group</h1>
            <p style="color: var(--text-dim); margin-bottom: 32px; font-size: 0.875rem;">Enter the 6-digit invite code
                shared
                by your roommate.</p>

            <form action="{{ route('groups.join.process') }}" method="POST">
                @csrf
                <div style="margin-bottom: 32px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 8px;"
                        for="invite_code">
                        Invite Code
                    </label>
                    <input type="text" id="invite_code" name="invite_code" maxlength="6"
                        value="{{ $invite_code ?? '' }}" placeholder="ABCDEF" required autofocus
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 12px; padding: 16px; color: var(--primary-light); outline: none; text-align: center; text-transform: uppercase; letter-spacing: 0.5em; font-weight: 700; font-size: 1.5rem; font-family: monospace;">
                    @error('invite_code')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 12px; text-align: center;">
                            {{ $message }}</p>
                    @enderror
                </div>

                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Join Group
                    </button>
                    <p style="text-align: center; color: var(--text-dim); font-size: 0.75rem; font-style: italic;">
                        By joining, you'll be able to see and add expenses in this group.
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
