<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function update(Request $request)
    {
        $this->validate($request,[
            'key' => 'required',
            'value' => 'required'
        ]);

        $setting = Setting::where(['key'=>$request['key']])->first();
        $setting->value = $request['value'];
        $setting->save();
        return $setting;
    }

    public function getSettings()
    {
        return Setting::all()->keyBy('key');
    }
}
