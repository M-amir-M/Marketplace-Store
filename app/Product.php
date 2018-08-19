<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'quantity',
        'amount',
        'price1',
        'price2',
        'price3',
        'min_order',
        'image',
        'description',
        'brand_id',
        'category_id',
    ];

    protected $appends = ['price', 'rate'];

    public function price()
    {
        if (Auth::check()) {
            if (User::find(Auth::user()->id)->hasRole('p_customer')) {
                return $this->price2;
            }
            if (User::find(Auth::user()->id)->hasRole('s_customer')) {
                return $this->price3;
            }
            return $this->price1;
        }
        return $this->price1;
    }

    public function getPriceAttribute()
    {
        return $this->price();
    }

    public function rate()
    {
        $rates = DB::select('select AVG(rate) as rate from product_user 
where product_id = :product_id
 AND rate IS NOT NULL ',
            ['product_id' => $this->id]);
        return $rates[0]->rate;
    }

    public function getRateAttribute()
    {
        return $this->rate();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('rate', 'favorite');//favorite and rate
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }
}
