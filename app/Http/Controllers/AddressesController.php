<?php

namespace App\Http\Controllers;

use App\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressesController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'local' => 'max:30',
            'address' => 'required|max:150',
        ]);

        $address = Address::create([
            'province_id' => $request['province'],
            'city_id' => $request['city'],
            'region' => $request['local'],
            'address' => $request['address']
        ]);

        $user = Auth::user();
        $user->address_id = $address->id;
        $user->save();

        return $address;
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:addresses,id',
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'local' => 'max:30',
            'address' => 'required|max:150',
        ]);

        $address = Address::find($request['id']);

        $address->update([
            'province_id' => $request['province'],
            'city_id' => $request['city'],
            'region' => $request['local'],
            'address' => $request['address']
        ]);

        return $address;
    }

    public function delete(Request $request)
    {
        $address = Address::find($request['id']);
        $address->delete();
        return $address;
    }
}
