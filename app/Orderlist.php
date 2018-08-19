<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orderlist extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'customer_id',
        'required_date',
        'shipped_date',
        'price',
        'description',
        'shipped_address',
        'status',
    ];

    protected $appends = ['cart'];

    public function cart()
    {
        $orders = $this->orders();
        $ids = $orders->pluck('product_id');
        $numbers = $orders->pluck('quantity');
        return ['ids' => $ids,'numbers'=>$numbers];
    }

    public function getCartAttribute()
    {
        return $this->cart();
    }

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class,'shipped_address');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
