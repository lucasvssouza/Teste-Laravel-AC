<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'receiver_id',
        'type',
        'previous_balance',
        'amount',
        'new_balance',
        'description',
        'cancelled'
    ];

public function receiver()
{
    return $this->belongsTo(User::class, 'receiver_id');
}

public function sender()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
