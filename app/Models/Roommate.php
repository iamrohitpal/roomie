<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roommate extends Model
{
    protected $fillable = ['name', 'email', 'avatar', 'user_id', 'phone'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'payer_id');
    }

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function paymentsSent()
    {
        return $this->hasMany(Settlement::class, 'sender_id');
    }

    public function paymentsReceived()
    {
        return $this->hasMany(Settlement::class, 'receiver_id');
    }

    public function getBalanceAttribute()
    {
        $spent = $this->expenses()->sum('amount');
        $owed = $this->splits()->sum('amount');
        $sent = $this->paymentsSent()->sum('amount');
        $received = $this->paymentsReceived()->sum('amount');

        return ($spent - $owed) + ($sent - $received);
    }
}
