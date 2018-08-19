<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'image',
    ];

    protected $appends = ['type_name'];


    public function getTypeNameAttribute()
    {
        return $this->type_name();
    }

    public function type_name()
    {
        $brand = $this;
        if($brand->type == 'market')
            return "عمده فروش(عرضه کننده)";
        return "تولیدی";
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function delete()
    {
        $this->products()->delete();
        return parent::delete();
    }

}
