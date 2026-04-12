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

        <h3 style="font-size: 0.875rem; margin: 32px 0 12px; color: var(--text-dim);">Danger Zone</h3>
        <div class="card" style="padding: 16px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                <h3 style="font-size: 1rem; margin: 0;"><i class="fa-solid fa-bell"
                        style="color: var(--primary-light); margin-right: 8px;"></i> Notifications</h3>
            </div>
            <p style="font-size: 0.75rem; color: var(--text-dim); margin-bottom: 20px;">
                Get real-time alerts when new expenses are added or roommates join.
            </p>
            @if (Auth::user()->fcm_token)
                <button type="button" onclick="disableNotifications()" class="btn"
                    style="width: 100%; background: rgba(251, 113, 133, 0.1); color: #fb7185; border: 1px solid rgba(251, 113, 133, 0.2);">
                    <i class="fa-solid fa-bell-slash" style="margin-right: 8px;"></i> Disable Notifications
                </button>
            @else
                <button type="button" onclick="requestPermission()" class="btn btn-secondary"
                    style="width: 100%; border: 1px solid var(--primary-light); color: var(--primary-light);">
                    <i class="fa-solid fa-bell-circle-check" style="margin-right: 8px;"></i> Enable Notifications
                </button>
            @endif
        </div>

        <div class="card" style="padding: 16px;">
            <p style="font-size: 0.75rem; color: var(--text-dim); margin-bottom: 20px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #fb7185; margin-right: 6px;"></i>
                Clearing group data will permanently delete all expenses and settlements for the selected group.
                <strong style="color: var(--text);">We strongly recommend downloading a CSV report first.</strong>
            </p>

            <form action="{{ route('groups.clear-data') }}" method="POST" id="clearDataForm">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 8px;">Select
                        Group to Manage</label>
                    <select name="group_id" id="groupSelect" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none; font-size: 0.875rem;">
                        @foreach ($allGroups as $group)
                            <option value="{{ $group->id }}"
                                {{ session('active_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <button type="button" id="downloadCsvBtn" onclick="downloadCsv()" class="btn btn-secondary"
                        style="width: 100%; border: 1px solid var(--primary-light); color: var(--primary-light);">
                        <i class="fa-solid fa-download" style="margin-right: 8px;"></i> Download CSV Report
                    </button>

                    @php
                        $activeGroupId = session('active_group_id');
                        $isOwnerOfActive = $ownedGroups->contains('id', $activeGroupId);
                        $ownedGroupsList = $ownedGroups->pluck('id')->toArray();
                    @endphp

                    @if($ownedGroups->count() > 0)
                        <div id="owner-only-section" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(251, 113, 133, 0.2);">
                             <p style="font-size: 0.7rem; color: #fb7185; margin-bottom: 8px; font-weight: 500;">
                                <i class="fa-solid fa-crown"></i> OWNER ACTIONS
                            </p>
                            <button type="button" id="clearDataBtn" onclick="confirmClear()" class="btn"
                                style="width: 100%; background: rgba(251, 113, 133, 0.1); color: #fb7185; border: 1px solid rgba(251, 113, 133, 0.2); transition: all 0.2s;">
                                <i class="fa-solid fa-trash-can" style="margin-right: 8px;"></i> Clear Selected Group Data
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <div class="logo" style="font-size: 1.25rem; opacity: 0.5;">Roomie</div>
            <p style="font-size: 0.625rem; color: var(--text-dim); margin-top: 8px;">MADE WITH PASSION BY ANTIGRAVITY</p>
        </div>
    </div>
@endsection

<script>
    function downloadCsv() {
        const id = document.getElementById('groupSelect').value;
        const baseUrl = "{{ route('groups.export-csv', ':id') }}";
        window.location.href = baseUrl.replace(':id', id);
    }

    function confirmClear() {
        const select = document.getElementById('groupSelect');
        const groupId = select.value;
        const groupName = select.options[select.selectedIndex].text;
        
        // Pass the owned groups list to JS for extra validation
        const ownedGroups = @json($ownedGroups->pluck('id'));
        
        if (!ownedGroups.includes(parseInt(groupId))) {
            showAlert('Unauthorized', 'You can only clear data for groups you created.', 'error');
            return;
        }

        showConfirm(
            'Clear Group Data',
            `CRITICAL ACTION: Are you absolutely sure you want to clear ALL expenses and settlements for "${groupName}"?\n\nThis will reset the group's ledger to zero. THIS CANNOT BE UNDONE.`,
            () => document.getElementById('clearDataForm').submit()
        );
    }

    document.addEventListener('DOMContentLoaded', () => {
        const theme = localStorage.getItem('theme');
        const text = document.getElementById('theme-status');
        if (text) text.innerText = theme === 'light' ? 'Light Mode' : 'Dark Mode';
    });
</script>
