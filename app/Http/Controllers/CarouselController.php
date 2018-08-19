<?php

namespace App\Http\Controllers;

use App\Carousel;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class CarouselController extends Controller
{
    public function getCarousels()
    {
        return Carousel::whereStatus(1)->get();
    }

    public function changeStatus(Request $request)
    {
        $carousel = Carousel::find($request['id'])->update([
            'status' => $request['status']
        ]);
        return (string)$carousel;
    }

    public function getCarousel(Carousel $carousel)
    {
        return $carousel;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:30',
            'image' => 'required',
        ]);

        $filename = 'default.png';

        if ($request->hasFile('image')) {
            $filename = str_slug($request->name, '-') . '-' . str_random(6) . '.' . $request->image->getClientOriginalExtension();

            $image = $request->file('image');
            $img_path = 'images/carousels/' . $filename;

            $img = Image::make($image->getRealPath());
            $img->resize(1100, 300)->save($img_path);
        }

        $carousel = Carousel::create([
            'title' => $request['name'],
            'image' => $filename
        ]);

        return $carousel;
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|max:30',
        ];

        if ($request->hasFile('image')) {
            $rules = [
                'name' => 'required|max:30',
                'image' => 'mimes:jpg,jpeg|max:4096',
            ];
        }

        $this->validate($request, $rules);

        $carousel = Carousel::find($request['id']);

        $filename = $carousel->image;
        if ($request->hasFile('image')) {
            $filename = str_slug($request->name, '-') . '-' . str_random(6) . '.' . $request->image->getClientOriginalExtension();

            $image = $request->file('image');
            $img_path = 'images/carousels/' . $filename;

            $img = Image::make($image->getRealPath());
            $img->resize(1100, 300)->save($img_path);

            if (file_exists(public_path('images/carousels/' . $carousel->image))) {
                unlink(public_path('images/carousels/' . $carousel->image));
            }
        }

        $carousel->update([
            'title' => $request['name'],
            'image' => $filename
        ]);

        return $carousel;
    }

    public function delete(Request $request)
    {
        $carousel = Carousel::find($request['id']);
        $carousel->delete();
        return $carousel;
    }
}
