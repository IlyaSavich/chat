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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('home');
    }

    public function chat()
    {
        return view('chat');
    }
}
