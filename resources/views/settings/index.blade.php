@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <h2 style="font-size: 1.125rem;">Settings</h2>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div
                style="padding: 16px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary-light);"></i>
                <div>
                    <p style="font-weight: 600; font-size: 0.875rem;">App Version</p>
                    <p style="font-size: 0.75rem; color: var(--text-dim);">1.0.0 (Build 20260322)</p>
                </div>
            </div>

            <div
                style="padding: 16px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-user-shield" style="color: var(--secondary);"></i>
                <div>
                    <p style="font-size: 0.75rem; color: var(--text-dim);">All data is stored locally on your device.</p>
                </div>
            </div>

            <a href="javascript:void(0)" onclick="toggleTheme()"
                style="text-decoration: none; padding: 16px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 12px; transition: background 0.2s;">
                <i class="fa-solid fa-moon" style="color: #fbbf24;"></i>
                <div style="flex: 1;">
                    <p style="font-weight: 600; font-size: 0.875rem; color: var(--text);">App Theme</p>
                    <p id="theme-status" style="font-size: 0.75rem; color: var(--text-dim);">Dark Mode</p>
                </div>
                <i class="fa-solid fa-repeat" style="font-size: 0.75rem; color: var(--text-dim);"></i>
            </a>
        </div>

        <h3 style="font-size: 0.875rem; margin: 32px 0 12px; color: var(--text-dim);">Maintenance</h3>
        <div class="card" style="padding: 0; overflow: hidden;">
            <form action="#" method="POST" onsubmit="return confirm('Are you sure you want to clear all data?')">
                <button type="button" class="btn"
                    style="width: 100%; border-radius: 0; background: transparent; color: #fb7185; justify-content: flex-start; padding: 16px; font-size: 0.875rem; font-weight: 500;">
                    <i class="fa-solid fa-trash-can" style="margin-right: 12px;"></i> Clear App Data
                </button>
            </form>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <div class="logo" style="font-size: 1.25rem; opacity: 0.5;">Roomie</div>
            <p style="font-size: 0.625rem; color: var(--text-dim); margin-top: 8px;">MADE WITH PASSION BY ANTIGRAVITY</p>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const theme = localStorage.getItem('theme');
        const text = document.getElementById('theme-status');
        if (text) text.innerText = theme === 'light' ? 'Light Mode' : 'Dark Mode';
    });
</script>
