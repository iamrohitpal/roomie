<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'amount', 'date', 'group_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function sender()
    {
        return $this->belongsTo(Roommate::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Roommate::class, 'receiver_id');
    }
}
