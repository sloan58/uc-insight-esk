<?php

namespace App\Http\Controllers;

use App\Models\Sql;
use Illuminate\Http\Request;

use App\Http\Requests;

class SqlController extends Controller
{
    /**
     * @var Sql
     */
    private $sql;

    /**
     * Create a new controller instance.
     */
    public function __construct(Sql $sql)
    {
        $this->middleware('auth');
        $this->sql = $sql;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $page_title = 'SQL';
        $page_description = 'Query';

        return view('sql.index', compact('page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        $sql = $request->input('sqlStatement');

        $data = $this->sql->executeQuery($sql);
        $format = $this->sql->getHeaders($data);

        $this->sql->firstOrCreate([
            'sqlhash' => md5($sql),
            'sql' => $sql
        ]);

        $page_title = 'SQL';
        $page_description = 'Query';

        return view('sql.index',compact('data','format','sql','page_title', 'page_description'));
    }

    public function show($sql)
    {

        $sql = $this->sql->find($sql);
        $sql = $sql->sql;

        $data = $this->sql->executeQuery($sql);
        $format = $this->sql->getHeaders($data);

        $page_title = 'SQL';
        $page_description = 'Query';

        return view('sql.index',compact('data','format','sql', 'page_title', 'page_description'));
    }

    public function history()
    {
        $sql = $this->sql->all();

        $page_title = 'SQL';
        $page_description = 'History';

        return view('sql.history', compact('sql','page_title', 'page_description'));

    }

    public function favorites()
    {
        $favorites = \Auth::user()->sqls()->get();

        $page_title = 'SQL';
        $page_description = 'Favorites';

        return view('sql.favorites', compact('favorites','page_title', 'page_description'));
    }
}
