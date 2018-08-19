<?php

namespace App\Http\Controllers;

use App\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function getPages()
    {
        return Page::all();
    }

    public function getPage(Page $page)
    {
        return $page;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:30',
            'body' => 'required',
        ]);

        $page = Page::create([
            'title' => $request['title'],
            'body' => $request['body']
        ]);

        return $page;
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:30',
            'body' => 'required',
        ]);

        $page = Page::find($request['id']);

        $page->update([
            'title' => $request['title'],
            'body' => $request['body']
        ]);

        return $page;
    }

    public function delete(Request $request)
    {
        $page = Page::find($request['id']);
        $page->delete();
        return $page;
    }
}
