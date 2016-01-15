<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;

class HomeController extends Controller
{

    public function index() {

        $page_title = "Home";
        $page_description = "This is the home page";

        return view('home', compact('page_title', 'page_description'));
    }

}
