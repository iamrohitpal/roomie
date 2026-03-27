@extends('layout')

@section('content')
    <div class="animate-fade-in">
        <h2 style="font-size: 1.125rem;">Settle Up</h2>
        <div class="card">
            <form action="{{ route('settlements.store') }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Who is
                        paying? (Sender)</label>
                    <select name="sender_id" required
                        style="width: 100%; background: #1e293b; border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                        @foreach ($roommates as $roommate)
                            <option value="{{ $roommate->id }}" {{ $senderId == $roommate->id ? 'selected' : '' }}>
                                {{ $roommate->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">To whom?
                        (Receiver)</label>
                    <select name="receiver_id" required
                        style="width: 100%; background: #1e293b; border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                        @foreach ($roommates as $roommate)
                            <option value="{{ $roommate->id }}">{{ $roommate->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Amount
                        (₹)</label>
                    <input type="number" step="0.01" name="amount" placeholder="0.00" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                </div>

                <div style="margin-bottom: 24px;">
                    <label
                        style="display: block; font-size: 0.75rem; color: var(--text-dim); margin-bottom: 4px;">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        style="width: 100%; background: rgba(255, 255, 255, 0.05); border: 1px solid var(--glass-border); border-radius: 10px; padding: 12px; color: white; outline: none;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Record Payment</button>
            </form>
        </div>
    </div>
@endsection
