@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.125rem; margin-bottom: 0;">Expenses History</h2>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.75rem;">Add
                New</a>
        </div>

        @forelse($expenses as $expense)
            <div class="card" style="padding: 16px; margin-bottom: 12px; position: relative; overflow: hidden;">
                <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--primary);"></div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div
                                style="width: 36px; height: 36px; border-radius: 18px; background: var(--glass-border); display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 0.8125rem;">
                                @if ($expense->payer->avatar)
                                    <img src="{{ $expense->payer->avatar }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ substr($expense->payer->name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <p style="font-weight: 600;">{{ $expense->description }}</p>
                                <p style="font-size: 0.75rem; color: var(--text-dim);">Paid by
                                    {{ $expense->payer->user_id === Auth::id() ? 'You' : $expense->payer->name }} •
                                    {{ date('D, M j, Y', strtotime($expense->date)) }}</p>
                            </div>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-dim); margin-top: 8px;">
                            <span
                                style="color: var(--text); font-weight: 500;">${{ number_format($expense->amount, 2) }}</span>
                        </p>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge"
                            style="background: rgba(99, 102, 241, 0.1); color: var(--primary-light);">{{ $expense->category }}</span>
                    </div>
                </div>

                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--glass-border);">
                    <p
                        style="font-size: 0.625rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                        Split with:</p>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        @foreach ($expense->splits as $split)
                            <div title="{{ $split->roommate->name }}: ${{ number_format($split->amount, 2) }}"
                                style="width: 24px; height: 24px; border-radius: 12px; background: var(--glass-border); border: 1px solid var(--primary-light); display: flex; align-items: center; justify-content: center; font-size: 0.625rem; font-weight: 700;">
                                {{ substr($split->roommate->name, 0, 1) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="card" style="text-align: center; color: var(--text-dim); padding: 60px 20px;">
                <i class="fa-solid fa-receipt" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.2;"></i>
                <p>No expenses recorded yet.</p>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary" style="margin-top: 16px;">Record First
                    Expense</a>
            </div>
        @endforelse
    </div>
@endsection
