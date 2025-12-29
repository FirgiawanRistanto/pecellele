<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $menus = Menu::all();
        return view('home', ['menus' => $menus]);
    }

    public function admin()
    {
        return view('admin');
    }
}
