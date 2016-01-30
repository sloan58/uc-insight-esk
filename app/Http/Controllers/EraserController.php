<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bulk;
use App\Http\Requests;
use App\Models\Device;
use App\Models\Eraser;
use Keboola\Csv\CsvFile;
use App\Jobs\EraseTrustList;
use Illuminate\Http\Request;
use App\Http\Requests\SubmitEraserRequest;
use App\Http\Requests\ProcessBulkEraserRequest;

/**
 * Class EraserController
 * @package App\Http\Controllers
 */
class EraserController extends Controller
{
    /**
     * @var \App\Models\Eraser
     */
    private $eraser;
    /**
     * @var \App\Bulk
     */
    private $bulk;
    /**
     * @var Device
     */
    private $device;

    /**
     * Create a new controller instance.
     *
     * @param \App\Models\Eraser $eraser
     * @param \App\Bulk|\App\Models\Bulk $bulk
     * @param Device $device
     * @return \App\Http\Controllers\EraserController
     */
    public function __construct(Eraser $eraser, Bulk $bulk,Device $device)
    {
        $this->eraser = $eraser;
        $this->bulk = $bulk;
        $this->device = $device;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function itlIndex()
    {
        $page_title = 'Eraser';
        $page_description = 'IT\'s';
        return view('eraser.itl.index', compact('page_title','page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function itlStore(Request $request)
    {

        $this->dispatch(
            new EraseTrustList([
                ['DeviceName' => $request->input('name'), 'type' => 'itl']
            ],\Auth::user()->activeCluster())
        );

        alert()->success('Processed Request.  Check table below for status.');
        return redirect('itl');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function ctlIndex()
    {
        $page_title = 'Eraser';
        $page_description = 'CTL\'s';

        $phones = $this->device->has('erasers')->get();
        foreach($phones as $phone)
        {
            $ctls[] = $phone->erasers()->where('type','CTL')->orderBy('updated_at','desc')->first();
        }

        return view('eraser.ctl.index', compact('ctls','page_title','page_description'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SubmitEraserRequest|\Illuminate\Http\Request $request
     * @return Response
     */
    public function ctlStore(SubmitEraserRequest $request)
    {
        $this->dispatch(
            new EraseTrustList([
                ['DeviceName' => $request->input('name'), 'type' => 'ctl']
            ],\Auth::user()->activeCluster())
        );

        alert()->success('Processed Request.  Check table below for status.');
        return redirect('ctl');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function bulkIndex()
    {
        $page_title = 'Bulk';
        $page_description = 'Eraser';

        return view('eraser.bulk.index', compact('page_title','page_description'));
    }

    /**
     * @param \App\Bulk|\App\Models\Bulk $bulk
     * @internal param $Bulk
     * @return Response
     */
    public function bulkShow(Bulk $bulk)
    {
        $page_title = 'Bulk';
        $page_description = 'Details';

        return view('eraser.bulk.show', compact('bulk','page_title','page_description'));
    }

    /**
     * @return Response
     */
    public function bulkCreate()
    {
        return view('eraser.bulk.create');
    }


    /**
     * @param ProcessBulkEraserRequest $request
     * @return \BladeView|bool|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bulkStore(ProcessBulkEraserRequest $request)
    {

        $file = $request->file('file');
        $fileName = $request->input('file_name');
        $fileName = $fileName ?: $file->getClientOriginalName();

        $bulk = $this->bulk->create([
            'file_name' => $fileName
        ]);

        if ($file->getClientMimeType() != "text/csv" && $file->getClientOriginalExtension() != "csv")
        {
            $bulk->result = "Invalid File Type";
            $bulk->mime_type = $file->getClientMimeType();
            $bulk->file_extension = $file->getClientOriginalExtension();
            $bulk->save();

            alert()->error('File type invalid.  Please use a CSV file format.')->persistent('Close');
            return redirect()->back();
        }

        $csvFile = new CsvFile($file);

        foreach($csvFile as $row)
        {
            $indexArray[] = $row;
        }

        for($i=0;$i<count($indexArray);$i++)
        {
            $eraserArray[$i]['DeviceName'] = $indexArray[$i][0];
            $eraserArray[$i]['type'] = $indexArray[$i][1];
            $eraserArray[$i]['bulk_id'] = $bulk->id;
        }

        $this->dispatch(
            new EraseTrustList($eraserArray,\Auth::user()->activeCluster())
        );

        $bulk->result = "Processed";
        $bulk->mime_type = $file->getClientMimeType();
        $bulk->file_extension = $file->getClientOriginalExtension();
        $bulk->process_id = $fileName . '-' . Carbon::now()->timestamp;
        $bulk->save();

        $file->move(storage_path() . '/uploaded_files/',$fileName);

        alert()->success("File loaded successfully!  Check the Bulk Process results below for status.");

        return redirect()->action('EraserController@bulkShow', [$bulk->id]);

    }
}
