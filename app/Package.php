<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'discount',
    ];

    protected $appends = ['price'];

    public function price()
    {
        $price = $this->products()->avg('price1');
        $price = $price - ($price*($this->discount/100));
        return $price;
    }

    public function getPriceAttribute()
    {
        return $this->price();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);//favorite and rate
    }
}
