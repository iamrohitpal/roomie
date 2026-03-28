@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.125rem; margin-bottom: 0;">Expenses History</h2>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.75rem;">Add
                New</a>
        </div>

        <form action="{{ route('expenses.index') }}" method="GET" style="margin-bottom: 24px;">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass"
                    style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); font-size: 0.875rem;"></i>
                <input type="text" name="search" placeholder="Search expenses..." value="{{ request('search') }}"
                    style="width: 100%; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; padding: 12px 12px 12px 40px; color: white; outline: none; font-size: 0.875rem;">
                @if (request('search'))
                    <a href="{{ route('expenses.index') }}"
                        style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: var(--text-dim); text-decoration: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        <div id="expense-list">
            @include('expenses._list')
        </div>

        @if ($expenses->hasMorePages())
            <div id="load-more-container" style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
                <button id="load-more-btn" data-url="{{ $expenses->nextPageUrl() }}" class="btn"
                    style="background: var(--glass-border); color: var(--text); border: 1px solid var(--glass-border); width: 100%;">
                    <i class="fa-solid fa-chevron-down" style="margin-right: 8px;"></i> Load More
                </button>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('load-more-btn');
            const listContainer = document.getElementById('expense-list');
            const loadMoreContainer = document.getElementById('load-more-container');

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
                            // Create a temporary container to parse the HTML
                            const temp = document.createElement('div');
                            temp.innerHTML = data.html;

                            // Append only the items
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
