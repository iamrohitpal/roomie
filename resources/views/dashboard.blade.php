@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div class="card"
            style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(14, 165, 233, 0.2)); border: 1px solid var(--primary-light);">
            <p style="color: var(--text-dim); font-size: 0.875rem;">Total Group Spending</p>
            <h1 style="font-size: 2.5rem; margin: 4px 0;">₹{{ number_format($totalSpending, 2) }}</h1>
            <p style="color: var(--text-dim); font-size: 0.8125rem;">Split among {{ $roommates->count() }} roommates</p>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" style="margin-top: 24px;">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass"
                    style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.875rem;"></i>
                <input type="text" name="search" placeholder="Search expenses or roommates..."
                    value="{{ request('search') }}"
                    style="width: 100%; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px 12px 12px 40px; color: white; outline: none; font-size: 0.875rem;">
                @if (request('search'))
                    <a href="{{ route('dashboard') }}"
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); text-decoration: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        <h2
            style="font-size: 1.125rem; margin-top: 32px; display: flex; justify-content: space-between; align-items: center;">
            Roommates Balances
            <a href="{{ route('roommates.index') }}"
                style="font-size: 0.75rem; color: var(--primary-light); text-decoration: none;">Manage</a>
        </h2>

        @forelse($roommates as $roommate)
            <div class="card"
                style="padding: 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div
                        style="width: 40px; height: 40px; border-radius: 20px; background: var(--glass-border); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary-light); overflow: hidden;">
                        @if ($roommate->avatar)
                            <img src="{{ $roommate->avatar }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ substr($roommate->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p style="font-weight: 600;">
                            {{ Auth::check() && $roommate->user_id === Auth::id() ? 'You' : $roommate->name }}</p>
                        <p style="font-size: 0.75rem; color: var(--text-dim);">
                            @if ($roommate->balance >= 0)
                                is owed
                            @else
                                owes
                            @endif
                        </p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight: 700; color: {{ $roommate->balance >= 0 ? '#4ade80' : '#fb7185' }}">
                        {{ $roommate->balance >= 0 ? '+' : '' }}₹{{ number_format(abs($roommate->balance), 2) }}
                    </p>
                    </p>
                </div>
            </div>
        @empty
            <div class="card" style="text-align: center; color: var(--text-dim); padding: 40px 20px;">
                <i class="fa-solid fa-users" style="font-size: 2rem; margin-bottom: 12px; opacity: 0.5;"></i>
                <p>No roommates added yet.</p>
                <a href="{{ route('roommates.index') }}" class="btn btn-primary" style="margin-top: 16px;">Add Roommate</a>
            </div>
        @endforelse

        <h2 style="font-size: 1.125rem; margin-top: 32px;">Recent Expenses</h2>
        <div id="recent-expenses-list">
            @include('expenses._recent')
        </div>

        @if ($recentExpenses->hasMorePages())
            <div id="load-more-dashboard-container" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
                <button id="load-more-dashboard-btn" data-url="{{ $recentExpenses->nextPageUrl() }}" class="btn"
                    style="background: var(--glass-border); color: var(--text); border: 1px solid var(--glass-border); width: 100%;">
                    <i class="fa-solid fa-chevron-down" style="margin-right: 8px;"></i> Load More
                </button>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more-dashboard-btn');
            const listContainer = document.getElementById('recent-expenses-list');
            const loadMoreContainer = document.getElementById('load-more-dashboard-container');

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
@endsection
