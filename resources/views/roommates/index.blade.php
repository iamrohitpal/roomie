@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <!-- Group Invitation Card [NEW] -->
        <div class="card"
            style="margin-bottom: 32px; border: 1px dashed var(--primary); background: rgba(var(--primary-rgb), 0.05);">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h3 style="font-size: 0.875rem; color: var(--text-dim); margin-bottom: 4px;">Group Invite Code</h3>
                    <p style="font-size: 1.5rem; font-weight: 700; letter-spacing: 2px; color: var(--primary);">
                        {{ \App\Models\Group::find(session('active_group_id'))->invite_code }}</p>
                </div>
                <button onclick="copyInviteLink()" class="btn btn-secondary" style="padding: 10px 20px; font-size: 0.875rem;">
                    <i class="fa-solid fa-share-nodes mr-2"></i> Share Link
                </button>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-dim); mt-2">Share this code with roommates to have them join
                this group.</p>
        </div>

        <h2 style="font-size: 1.125rem;">Add Roommate</h2>
        <div class="card">
            <form action="{{ route('roommates.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 20px;">
                    <div id="roommate-avatar-preview"
                        style="width: 60px; height: 60px; border-radius: 30px; background: rgba(255,255,255,0.05); border: 1px dashed var(--glass-border); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; cursor: pointer;">
                        <i class="fa-solid fa-camera" style="font-size: 1rem; color: var(--text-dim);"></i>
                        <input type="file" name="avatar" id="roommate-avatar-input" accept="image/*"
                            style="opacity: 0; position: absolute; inset: 0; cursor: pointer;">
                    </div>
                    <div style="flex: 1;">
                        <label
                            style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Name</label>
                        <input type="text" name="name" placeholder="e.g. John Doe" required
                            style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                    </div>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Phone
                        Number</label>
                    <div
                        style="display: flex; gap: 8px; align-items: center; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 4px 12px;">
                        <span style="color: var(--text-dim); font-size: 0.875rem;">+91</span>
                        <input type="tel" name="phone" placeholder="9876543210" required
                            style="flex: 1; background: transparent; border: none; padding: 8px 0; color: white; outline: none;">
                    </div>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Email
                        (Optional)</label>
                    <input type="email" name="email" placeholder="john@example.com"
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add Roommate</button>
            </form>
        </div>

        <h2 style="font-size: 1.125rem; margin-top: 32px;">Your Roommates</h2>
        @foreach ($roommates as $roommate)
            <div class="card"
                style="padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 44px; height: 44px; border-radius: 22px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: 700; overflow: hidden;">
                        @if ($roommate->avatar)
                            <img src="{{ $roommate->avatar }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ substr($roommate->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p style="font-weight: 600;">
                            {{ Auth::check() && $roommate->user_id === Auth::id() ? 'You' : $roommate->name }}</p>
                        <p style="font-size: 0.75rem; color: var(--text-dim);">{{ $roommate->email ?? 'No email' }}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <p
                        style="font-size: 0.75rem; font-weight: 600; color: {{ $roommate->balance >= 0 ? '#4ade80' : '#fb7185' }}">
                        {{ $roommate->balance >= 0 ? 'Owed' : 'Owes' }} ₹{{ number_format(abs($roommate->balance), 2) }}
                    </p>
                    <button type="button"
                        onclick="showConfirm('Remove Roommate', 'Are you sure you want to remove {{ $roommate->name }}? Their historical expenses will remain, but they will be removed from future splits.', () => document.getElementById('delete-form-{{ $roommate->id }}').submit())"
                        style="background: none; border: none; color: #fb7185; cursor: pointer; padding: 4px; font-size: 1rem; opacity: 0.7; transition: opacity 0.2s;"
                        onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.7">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    <form id="delete-form-{{ $roommate->id }}" action="{{ route('roommates.destroy', $roommate->id) }}"
                        method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        @endforeach

        @if ($otherUsersInGroup->count() > 0)
            <h2 style="font-size: 1.125rem; margin-top: 32px;">Pending Links (Members in Group)</h2>
            @foreach ($otherUsersInGroup as $user)
                <div class="card"
                    style="padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 44px; height: 44px; border-radius: 22px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-weight: 700; border: 1px solid var(--glass-border);">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}"
                                    style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                {{ substr($user->name ?? $user->phone, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p style="font-weight: 600;">{{ $user->name ?? 'User ' . substr($user->phone, -4) }}</p>
                            <p style="font-size: 0.75rem; color: var(--text-dim);">{{ $user->phone }}</p>
                        </div>
                    </div>
                    <form action="{{ route('roommates.add-user', $user->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.75rem;">Link
                            Roommate</button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>
    <script>
        function copyInviteLink() {
            const code = "{{ \App\Models\Group::find(session('active_group_id'))->invite_code }}";
            const text = "Join my Roomie group! Use code: " + code;
            navigator.clipboard.writeText(text).then(() => {
                alert('Invite info copied to clipboard!');
            });
        }

        document.getElementById('roommate-avatar-input')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('roommate-avatar-preview');
                    let img = preview.querySelector('img');
                    if (!img) {
                        img = document.createElement('img');
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        preview.insertBefore(img, preview.firstChild);
                    }
                    img.src = event.target.result;
                    const icon = preview.querySelector('i');
                    if (icon) icon.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
