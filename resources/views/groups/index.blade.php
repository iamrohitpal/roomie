@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 1.5rem; font-weight: 700;">My Groups</h1>
            <a href="{{ route('groups.create') }}" class="btn btn-primary" style="padding: 10px 16px;">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        @if ($groups->isEmpty())
            <div class="card" style="text-align: center; padding: 48px 24px;">
                <div
                    style="width: 80px; height: 80px; background: rgba(99, 102, 241, 0.1); border-radius: 40px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fa-solid fa-users-rectangle" style="font-size: 2.5rem; color: var(--primary-light);"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 8px;">No Groups Yet</h3>
                <p style="color: var(--text-dim); margin-bottom: 32px;">Create a group or join one using an invite code to
                    start splitting expenses.</p>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="{{ route('groups.create') }}" class="btn btn-primary" style="width: 100%;">Create New Group</a>
                    <a href="{{ route('groups.join') }}" class="btn btn-secondary" style="width: 100%;">Join Existing
                        Group</a>
                </div>
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach ($groups as $group)
                    <form action="{{ route('groups.switch', $group->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="card"
                            style="width: 100%; text-align: left; padding: 20px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; border: 1px solid {{ session('active_group_id') == $group->id ? 'var(--primary)' : 'var(--glass-border)' }}; background: {{ session('active_group_id') == $group->id ? 'rgba(99, 102, 241, 0.05)' : 'var(--card-bg)' }};">
                            <div>
                                <h3
                                    style="font-size: 1.125rem; font-weight: 700; color: {{ session('active_group_id') == $group->id ? 'var(--primary-light)' : 'var(--text)' }};">
                                    {{ $group->name }}
                                </h3>
                                <div
                                    style="display: flex; align-items: center; gap: 12px; margin-top: 8px; font-size: 0.75rem; color: var(--text-dim);">
                                    <span><i class="fa-solid fa-user-group" style="margin-right: 4px;"></i>
                                        {{ $group->users->count() }} members</span>
                                    <span><i class="fa-solid fa-key" style="margin-right: 4px;"></i>
                                        {{ $group->invite_code }}</span>
                                </div>
                            </div>
                            @if (session('active_group_id') == $group->id)
                                <div
                                    style="width: 10px; height: 10px; background: var(--primary); border-radius: 5px; box-shadow: 0 0 10px var(--primary);">
                                </div>
                            @else
                                <i class="fa-solid fa-chevron-right"
                                    style="color: var(--text-dim); font-size: 0.875rem;"></i>
                            @endif
                        </button>
                    </form>
                @endforeach

                <div style="display: flex; gap: 12px; margin-top: 16px;">
                    <a href="{{ route('groups.create') }}" class="btn btn-secondary"
                        style="flex: 1; font-size: 0.875rem;">Create Group</a>
                    <a href="{{ route('groups.join') }}" class="btn btn-secondary"
                        style="flex: 1; font-size: 0.875rem;">Join Group</a>
                </div>
            </div>
        @endif
    </div>
@endsection
