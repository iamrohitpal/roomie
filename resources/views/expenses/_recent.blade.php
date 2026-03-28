@forelse($recentExpenses as $expense)
    <div class="card expense-item" style="padding: 16px; margin-bottom: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div style="display: flex; gap: 12px; align-items: center;">
                <div
                    style="width: 32px; height: 32px; border-radius: 16px; background: var(--glass-border); display: flex; align-items: center; justify-content: center; overflow: hidden; font-size: 0.75rem;">
                    @if ($expense->payer->avatar)
                        <img src="{{ $expense->payer->avatar }}" style="width: 100%; height: 100%; object-fit: cover;">
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
    @if (!request()->ajax())
        <p style="text-align: center; color: var(--text-dim); margin-top: 12px;">No expenses yet.</p>
    @endif
@endforelse
