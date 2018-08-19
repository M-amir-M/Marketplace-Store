<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Role::class)->create(['name'=>'admin']);
        factory(\App\Role::class)->create(['name'=>'p_customer']);
        factory(\App\Role::class)->create(['name'=>'s_customer']);
        factory(\App\Role::class)->create(['name'=>'brand']);


        factory(\App\Brand::class,5)->create()->each(function ($brand){
            factory(\App\Category::class,3)->create()->each(function ($category) use ($brand){
                factory(\App\Product::class,15)->create(['brand_id' => $brand->id,'category_id'=>$category->id]);
            });
        });

        factory(\App\Province::class,rand(1,2))->create()->each(function ($province){
            factory(\App\City::class,rand(1,3))->create(['province_id' => $province->id])->each(function ($city){
                factory(\App\Address::class,3)->create(['province_id' => $city->province_id,'city_id'=>$city->id])->each(function ($address){
                    factory(\App\User::class,2)->create(['address_id' => $address->id]);
                });
            });
        });
    }
}
