<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['description', 'amount', 'payer_id', 'category', 'date'];

    public function payer()
    {
        return $this->belongsTo(Roommate::class, 'payer_id');
    }

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }
}
