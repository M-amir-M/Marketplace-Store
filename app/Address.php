<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'province_id',
        'city_id',
        'region',
        'address'
    ];

    protected $appends = ['city_name','province_name'];

    public function city_name()
    {
        return City::find($this->city_id)->name;
    }

    public function getCityNameAttribute()
    {
        return $this->city_name();
    }
    public function province_name()
    {
        return Province::find($this->province_id)->name;
    }

    public function getProvinceNameAttribute()
    {
        return $this->province_name();
    }

    public function orderlist()
    {
        return $this->hasOne(Orderlist::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
