<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'parent_id' => 'required',
        ]);

        $category = Category::create([
            'name' => $request['name'],
            'parent_id' => $request['parent_id'],
            'slug' => str_replace(' ','-',$request['name'])
        ]);

        $category->slug = str_replace(' ','-',$category->name)."-".$category->id;
        $category->save();

        return $this->getCategories();
    }

    public function update(Request $request)
    {
        $this->validate($request,[
            'name' => 'required',
            'id' => 'required',
        ]);
        $category = Category::find($request['id']);
        $category->update([
            'name' => $request['name'],
            'slug' => str_replace(' ','-',$request['name'])."-".$category->id
        ]);

        return $this->getCategories();
    }

    public function getCategories()
    {
        $categories = Category::nested()->get();
        return $categories;
    }

    public function delete(Request $request)
    {
        $category = Category::find($request['id']);
        $category->delete();

        $categories = Category::nested()->get();
        return $categories;
    }
}
