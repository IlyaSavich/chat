<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Show welcome page
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view('welcome');
    }

    public function chat()
    {
        return view('chat');
    }
}
