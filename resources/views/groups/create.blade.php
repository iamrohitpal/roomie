@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <a href="{{ route('groups.index') }}"
            style="display: inline-flex; align-items: center; color: var(--text-dim); text-decoration: none; margin-bottom: 24px; font-size: 0.875rem;">
            <i class="fa-solid fa-arrow-left" style="margin-right: 8px;"></i>
            Back to Groups
        </a>

        <div class="card">
            <h1 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 8px;">Create Group</h1>
            <p style="color: var(--text-dim); margin-bottom: 32px; font-size: 0.875rem;">Set up a new space to split expenses
                with your roommates.</p>

            <form action="{{ route('groups.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 8px;"
                        for="name">
                        Group Name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="e.g. Dream House, Summer Trip" required autofocus
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $errors->has('name') ? 'var(--accent)' : 'var(--glass-border)' }}; border-radius: 12px; padding: 12px 16px; color: white; outline: none; transition: border-color 0.2s;">
                    @error('name')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 8px;"><i
                                class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Create Group
                </button>
            </form>
        </div>
    </div>
@endsection
