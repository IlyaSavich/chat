<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    /**
     * Show welcome page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function welcome()
    {
        return view('welcome');
    }

    /**
     * Show chat page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chat()
    {
        return view('chat');
    }
}
