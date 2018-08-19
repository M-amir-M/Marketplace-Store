<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function getBrands()
    {
        return Brand::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
            'type' => 'required',
        ]);

        $brand = Brand::create([
            'name' => $request['name'],
            'type' => $request['type']
        ]);

        return $brand;
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:brands,id',
            'name' => 'required|max:30',
            'type' => 'required',
        ];

        $this->validate($request, $rules);

        $brand = Brand::find($request['id']);

        $brand->update([
            'name' => $request['name'],
            'type' => $request['type'],
        ]);

        return $brand;
    }

    public function delete(Request $request)
    {
        $brand = Brand::find($request['id']);
        $brand->delete();
        return $brand;
    }
}
