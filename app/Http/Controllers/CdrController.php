<?php

namespace App\Http\Controllers;

use App\Models\Cdr;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CdrController extends Controller
{
    /**
     * @var Cdr
     */
    private $cdr;

    /**
     * Create a new controller instance.
     */
    public function __construct(Cdr $cdr)
    {
        $this->middleware('auth');
        $this->cdr = $cdr;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cdrs = $this->cdr->all();

        $page_title = 'CDR';
        $page_description = 'List';

        return view('cdr.index', compact('cdrs','page_title','page_description'));
    }
}
