<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use App\Jobs\ProcessTwilioCall;
use App\Http\Controllers\Controller;

class AutoDialerController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('autodialer.index');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function placeCall(Request $request)
    {
        $number = substr($request->number, -10);
        $say = $request->say;
        $type = $request->type;

        $this->dispatch(new ProcessTwilioCall([[$number,$say,$type]]));

        Flash::success('Phone Call Submitted!  Check the call logs for status.');

        return redirect()->action('AutoDialerController@index');

    }

    /**
     * @return \Illuminate\View\View
     */
    public function bulkIndex()
    {
        $page_title = 'AutoDialer';
        $page_description = 'Bulk';

        return view('autodialer.bulk', compact('page_title','page_description'));
    }


    /**
     * @param Request $request
     * @throws AutoDialerException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bulkStore(Request $request)
    {
        $file = $request->file('file');

        if ($file->getClientMimeType() != "text/csv" && $file->getClientOriginalExtension() != "csv")
        {
            Flash::error('File type invalid.  Please use a CSV file format.');
            return redirect()->back();
        }

        $csvFile = new CsvFile($file);

        $csv = '';

        foreach($csvFile as $key => $row)
        {
            if(count($row) > 3)
            {
                $message = 'CSV Formatting Problem on Line ' . ++$key;
                Throw new AutoDialerException($message);
            }

            $csv[] = $row;
        }
        $this->dispatch(new ProcessTwilioCall($csv));

        Flash::success('Phone Call Submitted!  Check the call logs for status.');

        return redirect()->action('AutoDialerController@index');
    }
}
