@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <h2 style="font-size: 1.125rem;">Edit Expense</h2>
        <p style="font-size: 0.75rem; color: var(--text-dim); margin-bottom: 20px;">
            Note: You can only edit expenses within 10 minutes of creation.
        </p>

        <div class="card">
            <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Description</label>
                    <input type="text" name="description" value="{{ old('description', $expense->description) }}"
                        placeholder="e.g. Groceries, Rent, Electricity" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $errors->has('description') ? 'var(--accent)' : 'var(--glass-border)' }}; border-radius: 10px; padding: 12px; color: white; outline: none;">
                    @error('description')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Amount
                        (₹)</label>
                    <input type="number" step="0.01" name="amount" id="main_amount"
                        value="{{ old('amount', $expense->amount) }}" placeholder="0.00" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $errors->has('amount') ? 'var(--accent)' : 'var(--glass-border)' }}; border-radius: 10px; padding: 12px; color: white; outline: none;">
                    @error('amount')
                        <p style="color: var(--accent); font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Date</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d', strtotime($expense->date))) }}"
                        required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                </div>

                <div style="margin-bottom: 16px;">
                    <label
                        style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Category</label>
                    <input type="text" name="category" value="{{ old('category', $expense->category) }}"
                        placeholder="e.g. Food, Travel, Home"
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                </div>

                <h3 style="font-size: 0.875rem; margin: 24px 0 12px;">Split Among</h3>
                <div id="splits-container">
                    @foreach ($roommates as $roommate)
                        @php
                            $splitAmount = $expense->splits->where('roommate_id', $roommate->id)->first()->amount ?? 0;
                        @endphp
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding: 8px; border-radius: 8px; background: rgba(255, 255, 255, 0.02);">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" {{ $splitAmount > 0 ? 'checked' : '' }} class="roommate-check"
                                    data-id="{{ $roommate->id }}" style="accent-color: var(--primary);">
                                <span style="font-size: 0.875rem;">{{ $roommate->name }}</span>
                            </div>
                            <input type="number" step="0.01" name="splits[{{ $roommate->id }}]" class="split-input"
                                data-id="{{ $roommate->id }}" value="{{ old('splits.' . $roommate->id, $splitAmount) }}"
                                placeholder="0.00"
                                style="width: 80px; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 8px; padding: 8px; color: white; text-align: right; outline: none;">
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: center; margin-bottom: 24px;">
                    <button type="button" id="split-equally" class="btn"
                        style="padding: 8px 16px; font-size: 0.75rem; background: var(--glass-border); color: var(--text-dim);">
                        Split Equally
                    </button>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Expense</button>
                <a href="{{ route('dashboard') }}" class="btn"
                    style="width: 100%; display: block; text-align: center; margin-top: 12px; background: rgba(255, 255, 255, 0.05); text-decoration: none; color: white; padding: 12px; border-radius: 12px;">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('main_amount');
            const splitBtn = document.getElementById('split-equally');
            const splitInputs = document.querySelectorAll('.split-input');
            const checks = document.querySelectorAll('.roommate-check');

            function updateSplits() {
                const total = parseFloat(amountInput.value) || 0;
                const activeChecks = Array.from(checks).filter(c => c.checked);

                if (activeChecks.length === 0) return;

                const splitAmount = (total / activeChecks.length).toFixed(2);
                let runningTotal = 0;

                splitInputs.forEach(input => {
                    const id = input.dataset.id;
                    const checkbox = Array.from(checks).find(c => c.dataset.id === id);

                    if (checkbox.checked) {
                        input.value = splitAmount;
                        runningTotal += parseFloat(splitAmount);
                    } else {
                        input.value = '0.00';
                    }
                });

                // Adjust for rounding
                const diff = (total - runningTotal).toFixed(2);
                if (activeChecks.length > 0 && diff !== '0.00') {
                    const lastId = activeChecks[activeChecks.length - 1].dataset.id;
                    const lastInput = Array.from(splitInputs).find(i => i.dataset.id === lastId);
                    lastInput.value = (parseFloat(lastInput.value) + parseFloat(diff)).toFixed(2);
                }
            }

            splitBtn.addEventListener('click', updateSplits);
            amountInput.addEventListener('input', updateSplits);
            checks.forEach(c => c.addEventListener('change', updateSplits));
        });
    </script>
@endsection
