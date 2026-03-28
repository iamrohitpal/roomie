@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h1 style="font-size: 1.5rem; font-weight: 700;">My Groups</h1>
            <a href="{{ route('groups.create') }}" class="btn btn-primary" style="padding: 10px 16px;">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        <form action="{{ route('groups.index') }}" method="GET" style="margin-bottom: 24px;">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass"
                    style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.875rem;"></i>
                <input type="text" name="search" placeholder="Search groups..." value="{{ request('search') }}"
                    style="width: 100%; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px 12px 12px 40px; color: white; outline: none; font-size: 0.875rem;">
                @if (request('search'))
                    <a href="{{ route('groups.index') }}"
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); text-decoration: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

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
            <div id="groups-list">
                @include('groups._list')
            </div>

            @if ($groups->hasMorePages())
                <div id="load-more-groups-container" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
                    <button id="load-more-groups-btn" data-url="{{ $groups->nextPageUrl() }}" class="btn"
                        style="background: var(--glass-border); color: var(--text); border: 1px solid var(--glass-border); width: 100%;">
                        <i class="fa-solid fa-chevron-down" style="margin-right: 8px;"></i> Load More
                    </button>
                </div>
            @endif

            <div style="display: flex; gap: 12px; margin-top: 16px;">
                <a href="{{ route('groups.create') }}" class="btn btn-secondary"
                    style="flex: 1; font-size: 0.875rem;">Create
                    Group</a>
                <a href="{{ route('groups.join') }}" class="btn btn-secondary" style="flex: 1; font-size: 0.875rem;">Join
                    Group</a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more-groups-btn');
            const listContainer = document.getElementById('groups-list');
            const loadMoreContainer = document.getElementById('load-more-groups-container');

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    const url = loadMoreBtn.getAttribute('data-url');
                    loadMoreBtn.disabled = true;
                    loadMoreBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            const temp = document.createElement('div');
                            temp.innerHTML = data.html;

                            while (temp.firstChild) {
                                listContainer.appendChild(temp.firstChild);
                            }

                            if (data.next_page) {
                                loadMoreBtn.setAttribute('data-url', data.next_page);
                                loadMoreBtn.disabled = false;
                                loadMoreBtn.innerHTML =
                                    '<i class="fa-solid fa-chevron-down"></i> Load More';
                            } else {
                                loadMoreContainer.remove();
                            }
                        })
                        .catch(err => {
                            console.error('Error loading more:', err);
                            loadMoreBtn.innerHTML = 'Error. Try again.';
                            loadMoreBtn.disabled = false;
                        });
                });
            }
        });
    </script>
    </div>
@endsection
