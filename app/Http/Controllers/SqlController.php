<?php

namespace App\Http\Controllers;

use App\Models\Sql;
use App\Libraries\Utils;
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
     * @param Sql $sql
     */
    public function __construct(Sql $sql)
    {
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

        if (! \Auth::user()->activeCluster()) {
            alert()->error('Please set your active CUCM Cluster')->persistent('Close');
            return redirect()->action('SqlController@index');
        }

        $data = Utils::executeQuery($sql,\Auth::user()->activeCluster());

        if(is_null($data))
        {
            alert()->error('No Results Found')->persistent('Close');
            return redirect()->back();
        }
        
        $format = $this->sql->getHeaders($data);

        $this->sql->firstOrCreate([
            'sqlhash' => md5($sql),
            'sql' => $sql
        ]);

        $page_title = 'SQL';
        $page_description = 'Query';

        return view('sql.show',compact('data','format','sql','page_title', 'page_description'));
    }

    public function show($sql)
    {

        $sql = $this->sql->find($sql);
        $sql = $sql->sql;

        if (! \Auth::user()->activeCluster()) {
            alert()->error('Please set your active CUCM Cluster')->persistent('Close');
            return redirect()->action('SqlController@index');
        }

        $data = Utils::executeQuery($sql,\Auth::user()->activeCluster());

        if(is_null($data))
        {
            alert()->error('No Results Found')->persistent('Close');
            return redirect()->back();
        }
        
        $format = $this->sql->getHeaders($data);

        $page_title = 'SQL';
        $page_description = 'Query';

        return view('sql.show',compact('data','format','sql', 'page_title', 'page_description'));
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
