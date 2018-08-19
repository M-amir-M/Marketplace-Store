<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Order;
use App\Orderlist;
use App\Package;
use App\Product;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ProductsController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->hasRole('brand'))
            $request['brand_id'] = \auth()->user()->brand_id;
        $this->validate($request, [
            'name' => 'required|max:30',
            'brand_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required',
            'amount' => 'required',
            'price1' => 'required',
            'price2' => 'required',
            'price3' => 'required',
            'min_order' => 'required',
            'description' => 'max:255',
            'image' => 'mimes:jpg,jpeg|max:4096',
        ]);

        $filename = 'default.png';
        if ($request->hasFile('image')) {
            $filename = str_slug($request->name, '-') . '-' . str_random(6) . '.' . $request->image->getClientOriginalExtension();

            $image = $request->file('image');
            $img_path = 'images/products/' . $filename;
            $thumbnail_path = 'images/products/tn-' . $filename;

            $img = Image::make($image->getRealPath());
            $img->resize(250, 250)->save($thumbnail_path);
            $img->resize(1000, 1000)->save($img_path);
        }

        $product = Product::create([
            'name' => $request['name'],
            'brand_id' => $request['brand_id'],
            'category_id' => $request['category_id'],
            'quantity' => $request['quantity'],
            'amount' => $request['amount'],
            'price1' => $request['price1'],
            'price2' => $request['price2'],
            'price3' => $request['price3'],
            'min_order' => $request['min_order'],
            'status' => Auth::user()->hasRole('admin') ? 1 : 0,
            'description' => $request['description'],
            'image' => $filename,
        ]);

        return $product;
    }

    public function update(Request $request)
    {
        if (Auth::user()->hasRole('brand') && $request['brand_id'] != auth()->user()->brand_id)
            return;
        $rules = [
            'name' => 'required|max:30',
            'brand_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required',
            'amount' => 'required',
            'price1' => 'required',
            'price2' => 'required',
            'min_order' => 'required',
            'description' => 'max:255'
        ];

        if ($request->hasFile('image')) {
            $rules = [
                'name' => 'required|max:30',
                'brand_id' => 'required|exists:users,id',
                'category_id' => 'required|exists:categories,id',
                'quantity' => 'required',
                'amount' => 'required',
                'price1' => 'required',
                'price2' => 'required',
                'min_order' => 'required',
                'description' => 'max:255',
                'image' => 'mimes:jpg,jpeg|max:4096',
            ];
        }

        $this->validate($request, $rules);

        $product = Product::find($request['id']);

        $filename = $product->image;
        if ($request->hasFile('image')) {
            $filename = str_slug($request->name, '-') . '-' . str_random(6) . '.' . $request->image->getClientOriginalExtension();

            $image = $request->file('image');
            $img_path = 'images/products/' . $filename;
            $thumbnail_path = 'images/products/tn-' . $filename;

            $img = Image::make($image->getRealPath());
            $img->resize(250, 250)->save($thumbnail_path);
            $img->resize(1000, 1000)->save($img_path);

            if (file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }
            if (file_exists(public_path('images/products/tn-' . $product->image))) {
                unlink(public_path('images/products/tn-' . $product->image));
            }
        }

        $product->update([
            'name' => $request['name'],
            'brand_id' => $request['brand_id'],
            'category_id' => $request['category_id'],
            'quantity' => $request['quantity'],
            'unit' => $request['unit'],
            'amount' => $request['amount'],
            'price1' => $request['price1'],
            'price2' => $request['price2'],
            'min_order' => $request['min_order'],
            'status' => Auth::user()->hasRole('admin') ? 1 : 0,
            'description' => $request['description'],
            'image' => $filename,
        ]);

        return $product;
    }

    public function getProducts()
    {
        return Product::where(['status' => 1])->orderBy('created_at', 'desc')->with('brand')->get();
    }

    public function getProduct($id)
    {
        $product = Product::with('brand')->find($id);
        $buy = false;
        $rate = 0;
        $favorite = false;
        if (Auth::check()) {
            $query = DB::select('select * from product_user where user_id = :user_id AND product_id = :product_id',
                ['product_id' => $id, 'user_id' => Auth::user()->id]);
            if (count($query) > 0) {
                if ($query[0]->favorite != null)
                    $favorite = $query[0]->favorite == 1 ? true : false;
                $rate = $query[0]->rate == null ? 0 : $query[0]->rate;
            } else {
                $favorite = false;
            }
            $orderlist = Orderlist::where(['customer_id' => Auth::id(), 'status' => 'delivered'])
                ->get()
                ->pluck('id')->toArray();
            $orders = Order::whereIn('id', $orderlist)
                ->get()
                ->pluck('id')->toArray();
            $buy = in_array($id, $orders);

        }

        return ['product' => $product, 'favorite' => $favorite, 'buy' => $buy, 'rate' => $rate];
    }

    public function changeFavorite(Request $request)
    {
        $favorite = DB::select('select * from product_user where user_id = :user_id AND product_id = :product_id',
            ['product_id' => $request['product'], 'user_id' => Auth::user()->id]);

        if (count($favorite) > 0) {
            DB::update('update product_user set favorite = ? where product_id = ? AND user_id = ?', [$request['favorite'], $request['product'], Auth::id()]);
        } else {
            DB::insert('INSERT INTO product_user (product_id, user_id,favorite) VALUES (?,?,?)', [$request['product'], Auth::id(), $request['favorite']]);
        }
    }

    public function getFavorite()
    {
        $favorite = DB::select('select product_id from product_user where user_id = :user_id AND favorite = 1',
            ['user_id' => Auth::user()->id]);

        $favorite = array_column($favorite, 'product_id');

        $products = Product::with('brand')->whereIn('id', $favorite)->get();
        return $products;
    }

    public function changeRate(Request $request)
    {
        $query = DB::select('select * from product_user where user_id = :user_id AND product_id = :product_id',
            ['product_id' => $request['product'], 'user_id' => Auth::user()->id]);

        if (count($query) > 0) {
            DB::update('update product_user set rate = ? where product_id = ? AND user_id = ?', [$request['rate'], $request['product'], Auth::id()]);
        } else {
            DB::insert('INSERT INTO product_user (product_id, user_id,rate) VALUES (?,?,?)', [$request['product'], Auth::id(), $request['rate']]);
        }
        return 'success';
    }

    public function subCat($id_level1)
    {
        $ids = [];
        $cats = Category::where('parent_id', $id_level1)->get()->pluck('id')->toArray();
        $ids = Category::whereIn('parent_id', $cats)->get()->pluck('id')->toArray();
        $ids = array_merge($cats, $ids);
        return $ids;
    }

    public function getHomeProducts()
    {
        $cats = Category::where(['parent_id' => 0])->get();
        $products = [];
        foreach ($cats as $cat) {
            $ids = $this->subCat($cat['id']);
            array_push($ids, $cat['id']);
            $query = Product::where(['status' => 1])->whereIn('category_id', $ids)->with('brand')->orderBy('created_at', 'desc')->take(20)->get();
            array_push($products, [$cat, $query]);
        }
        return $products;
    }

    public function getCategory($category_id)
    {
        $category = Category::find($category_id);
        $parents = [];
        array_push($parents, [$category->id, $category->name]);

        while ($category->parent_id != 0) {
            $category = Category::find($category->parent_id);
            array_push($parents, [$category->id, $category->name]);
        }
        return $parents;
    }

    public function search($term, $page, $cats, $brands)
    {
        $status = [1];
        if (Auth::check())
            if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('brand'))
                $status = [0, 1, null];
        $term = explode('=', $term)[1];
        $page = explode('=', $page)[1];
        $cats = explode('=', $cats)[1];
        $brands = explode('=', $brands)[1];

        if ($cats == '' && $brands == '') {
            $brand_ids = Product::where('name', 'like', '%' . $term . '%')
                ->whereIn('status', $status)
                ->select('brand_id')
                ->distinct('brand_id')
                ->get()
                ->pluck('brand_id');
            $products = Product::where('name', 'like', '%' . $term . '%')
                ->whereIn('status', $status)
                ->with('brand')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'page', $page);
            $brands = Brand::find($brand_ids);

            return ['products' => $products, 'brands' => $brands];
        }
        if ($cats != '' && $brands == '') {
            $brand_ids = Product::where('name', 'like', '%' . $term . '%')
                ->whereIn('status', $status)
                ->whereIn('category_id', explode('-', $cats))
                ->select('brand_id')
                ->distinct('brand_id')
                ->get()
                ->pluck('brand_id');
            $products = Product::where('name', 'like', '%' . $term . '%')
                ->whereIn('status', $status)
                ->whereIn('category_id', explode('-', $cats))
                ->with('brand')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'page', $page);
            $brands = Brand::find($brand_ids);

            return ['products' => $products, 'brands' => $brands];
        }
        if ($cats == '' && $brands != '') {
            $products = Product::where('name', 'like', '%' . $term . '%')
                ->whereIn('status', $status)
                ->whereIn('brand_id', explode('-', $brands))
                ->with('brand')
                ->orderBy('created_at', 'desc')
                ->paginate(20, ['*'], 'page', $page);
            $brands = Brand::find(explode('-', $brands));

            return ['products' => $products, 'brands' => $brands];
        }

        $products = Product::where('name', 'like', '%' . $term . '%')//first
        ->whereIn('status', $status)
            ->whereIn('category_id', explode('-', $cats))
            ->whereIn('brand_id', explode('-', $brands))
            ->with('brand')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'page', $page);

        $brands = Brand::find(explode('-', $brands));//second بعد از اولی مقدار دهی بشه


        return ['products' => $products, 'brands' => $brands];
    }

    public function getCartProducts($cart)
    {
        $cart = json_decode($cart);
        $cart_ids= $cart->ids;
        $products =[];
        foreach ($cart->types as $key => $type){
            if($type == 'package'){
                $package = Package::find($cart_ids[$key]);
                $package['type'] = 'package';
                array_push($products,$package);
            }
            if($type == 'product'){
                $product = Product::find($cart_ids[$key]);
                $product['type'] = 'product';
                array_push($products,$product);
            }
        }
        return $products;
    }

    public function delete(Request $request)
    {
        $product = Product::find($request['id']);
        $product->delete();
        return $product;
    }

    public function getPackage($id)
    {
        $package = Package::with(['products' => function ($query) {
            $query->select('id');
        }])->find($id);
        return $package;
    }

    public function package($id)
    {
        $package = Package::with('products')->find($id);
        return $package;

    }

    public function getPackages()
    {
        $packages = Package::all();
        return $packages;
    }



    public function addToPackage(Request $request)
    {
        Package::find($request['package_id'])->products()->attach($request['product_id']);
    }

    public function changeStatus(Request $request)
    {
        $product = Product::findOrFail($request['id']);
        $product->status = $request['status'];
        $product->save();
        return $product;
    }

    public function deleteFromPackage(Request $request)
    {
        Package::find($request['package_id'])->products()->detach($request['product_id']);
    }

    public function searchPackage($term, $page)
    {
        $term = explode('=', $term)[1];
        $page = explode('=', $page)[1];

        $packages = Package::where('name', 'like', '%' . $term . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'page', $page);


        return ['packages' => $packages];
    }

    public function storePackage(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
        ]);

        $package = Package::create([
            'name' => $request['name']
        ]);

        return $package;
    }

    public function updatePackage(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
        ]);
        $package = Package::find($request['id']);

        $package->update([
            'name' => $request['name']
        ]);
    }

    public function deletePackage(Request $request)
    {
        $package = Package::find($request['id']);
        $package->delete();
        return $package;
    }
}
