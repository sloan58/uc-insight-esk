<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;

class HomeController extends Controller
{

    public function index() {

        return view('home', compact('page_title', 'page_description'));
    }

}
