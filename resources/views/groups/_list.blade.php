@foreach ($groups as $group)
    <form action="{{ route('groups.switch', $group->id) }}" method="POST">
        @csrf
        <button type="submit" class="card group-item"
            style="width: 100%; text-align: left; padding: 20px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 16px; border: 1px solid {{ session('active_group_id') == $group->id ? 'var(--primary)' : 'var(--glass-border)' }}; background: {{ session('active_group_id') == $group->id ? 'rgba(99, 102, 241, 0.05)' : 'var(--card-bg)' }};">
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
                <i class="fa-solid fa-chevron-right" style="color: var(--text-dim); font-size: 0.875rem;"></i>
            @endif
        </button>
    </form>
@endforeach
