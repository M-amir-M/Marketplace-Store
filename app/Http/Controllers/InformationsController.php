<?php

namespace App\Http\Controllers;

use App\Information;
use Illuminate\Http\Request;

class InformationsController extends Controller
{
    public function getInformations()
    {
        return Information::all();
    }

    public function changeStatus(Request $request)
    {
        $info = Information::find($request['id'])->update([
            'status' => $request['status']
        ]);
        return (string)$info;
    }

    public function getActiveInfos()
    {
        return Information::whereStatus(1)->get();
    }

    public function getInformation(Information $information)
    {
        return $information;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:30',
            'body' => 'required',
        ]);

        $information = Information::create([
            'title' => $request['title'],
            'info' => $request['body']
        ]);

        return $information;
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:30',
            'body' => 'required',
        ]);

        $information = Information::find($request['id']);

        $information->update([
            'title' => $request['title'],
            'info' => $request['body']
        ]);

        return $information;
    }

    public function delete(Request $request)
    {
        $information = Information::find($request['id']);
        $information->delete();
        return $information;
    }
}
