<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait {
        EntrustUserTrait::restore insteadof SoftDeletes;
    }
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'brand_id',
        'email',
        'phone',
        'mobile',
        'address_id',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'verifyToken'

    ];

    protected $appends = ['role_user', 'buy_amount'];

    public function role_user()
    {
        return $this->roles()->get()->pluck('name');
    }

    public function getRoleUserAttribute()
    {
        return $this->role_user();
    }

    public function buy_amount()
    {
        $amount = Transaction::where(['customer_id' => $this->id, 'status' => 1])->sum('price');
        $items = ['1-star', '2-star', '3-star', '4-star', '5-star'];
        $star = 0;
        foreach ($items as $key => $item) {
            $setting = Setting::where('key', $item)->first();
            if (count($setting) > 0)
                if ($setting->value > 0)
                    if ($amount > $setting->value)
                        $star = $key + 1;
        }
        return ['amount' => $amount, 'star' => $star];
    }


    public function getBuyAmountAttribute()
    {
        return $this->buy_amount();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('favorite', 'favorite');//favorite and favorite
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderlists()
    {
        return $this->hasMany(Orderlist::class, 'customer_id');
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();

    }
}
