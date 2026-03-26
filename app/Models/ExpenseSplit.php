<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseSplit extends Model
{
    protected $fillable = ['expense_id', 'roommate_id', 'amount'];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function roommate()
    {
        return $this->belongsTo(Roommate::class);
    }
}
