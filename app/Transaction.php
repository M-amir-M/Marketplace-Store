<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'bank',
        'price',
        'orderlist_id',
        'customer_id',
        'track_code',
        'return_code',
        'result_code',
        'status',
    ];

    public function orderlist()
    {
        return $this->belongsTo(Orderlist::class);
    }
}
