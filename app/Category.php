<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nestable\NestableTrait;

class Category extends Model
{
    use NestableTrait;
    use SoftDeletes;

    public static $cat_ids = [];

    protected $parent = 'parent_id';

    protected $fillable = [
        'name',
        'parent_id',
        'slug',
        'image',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function delete()
    {
        $this->products()->delete();
        return parent::delete();
    }


}


