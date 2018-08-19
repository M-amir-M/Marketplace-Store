<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'product_id',
        'orderlist_id',
        'unit_price',
        'quantity',
        'discount',
    ];

    protected $appends = ['order_product'];

    public function order_product()
    {
        return Product::find($this->product_id);
    }

    public function getOrderProductAttribute()
    {
        return $this->order_product();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderlist()
    {
        return $this->belongsTo(Orderlist::class);
    }


}
