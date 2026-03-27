@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div class="card"
            style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(14, 165, 233, 0.2)); border: 1px solid var(--primary-light);">
            <p style="color: var(--text-dim); font-size: 0.875rem;">Total Group Spending</p>
            <h1 style="font-size: 2.5rem; margin: 4px 0;">₹{{ number_format($totalSpending, 2) }}</h1>
            <p style="color: var(--text-dim); font-size: 0.8125rem;">Split among {{ $roommates->count() }} roommates</p>
        </div>

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
        @forelse($recentExpenses as $expense)
            <div class="card" style="padding: 16px; margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div
                            style="width: 32px; height: 32px; border-radius: 16px; background: var(--glass-border); display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 0.75rem;">
                            @if ($expense->payer->avatar)
                                <img src="{{ $expense->payer->avatar }}"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ substr($expense->payer->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p style="font-weight: 600;">{{ $expense->description }}</p>
                            <p style="font-size: 0.75rem; color: var(--text-dim);">Paid by {{ $expense->payer->name }} •
                                {{ $expense->date }}</p>
                        </div>
                    </div>
                    <p style="font-weight: 700;">₹{{ number_format($expense->amount, 2) }}</p>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-dim); margin-top: 12px;">No expenses yet.</p>
        @endforelse
    </div>
@endsection
