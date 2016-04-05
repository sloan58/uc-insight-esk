<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Storage;
use App\Http\Requests;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

/**
 * Class IosConfigGeneratorController
 * @package App\Http\Controllers
 */
class IosConfigGeneratorController extends Controller
{
    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index()
    {
        $files = Storage::files('ios-config-templates/');

        $shortNames= [];
        foreach($files as $file)
        {
            $chunks = explode('/',$file);
            $shortNames[] = array_pop($chunks);
        }

        return view('ios-config-generator.index', compact('shortNames'));
    }

    /**
     * @param $fileName
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function create($fileName)
    {
        //Get the file from storage
        $contents = Storage::get('ios-config-templates/' .  $fileName);

        //Create an empty array to fill with config headers and vars
        $viewVariables = [];

        //Find all the headers (begin and end)
        preg_match_all('/{.+?}/',$contents,$begTags);

        //Loop each header
        foreach($begTags[0] as $index => $tag)
        {
            //The headers are stored with the 'start' marker in
            //even indices.  If it's not even, it's an 'end' marker
            //and we can continue;
            if($index % 2 != 0) continue;

            //Rip out { } from the headers to display nicely in HTML
            $viewHeader = trim(str_replace(['{','}'],'',$tag));

            //Get the beginning position of the section
            $beginPos = strpos($contents,$tag);
            //Get the ending position of the section
            $endPos = strpos($contents,$begTags[0][$index + 1]);
            //Get the length between beginning and end
            $length = abs($beginPos - $endPos);

            //Extract the text between the header markers
            $between = substr($contents, $beginPos, $length);

            //Get an array of variable within the section
            preg_match_all('/<<.+?>>/',$between,$matches);
            $matches = $matches[0];

            //Loop the variables and create friendly display
            //names for the HTML
            foreach($matches as $match)
            {
                preg_match('/<<(.*)>>/',$match,$out);
                $viewVariables[$viewHeader][] = $out;

            }

        }

        return view('ios-config-generator.create',compact('viewVariables','fileName'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function store(Request $request)
    {
        //Get the form input
        $input = $request->input();

        //Get the file from storage
        $contents = Storage::get('ios-config-templates/' .  $input['fileName']);

        //Find all the variables in the file
        preg_match_all('/<<.+?>>/',$contents,$matches);
        $matches = $matches[0];

        //Loop each variable and replace with the
        //actual data submitted in the form
        foreach($matches as $match)
        {
            $contents = str_replace($match,$input[$match],$contents);
        }

        //Find all the headers (begin and end)
        preg_match_all('/{.+?}/',$contents,$begTags);

        //Loop each header to remove it from the final file
        foreach($begTags[0] as $index => $tag)
        {
            $contents = str_replace($tag,'',$contents);
        }

        $tempFileName = 'ios-config-templates/temp/'. $input['fileName'] . '-' . 'completed' . '-' . \Carbon\Carbon::now()->timestamp . '.txt';

        Storage::put($tempFileName,$contents);

        return response()->download(storage_path() . '/' . $tempFileName)->deleteFileAfterSend(true);

    }

    public function loadFile(Request $request)
    {
        $file = $request->file('file');

        if ($file->getClientMimeType() != "text" && $file->getClientOriginalExtension() != "txt")
        {
            alert()->error('File type invalid.  Please use a .txt file format.');
            return redirect()->back();
        }

        $file->move(storage_path() . '/ios-config-templates/', $file->getClientOriginalName());

        alert()->success('New IOS Config Submitted!');
        return redirect()->back();
    }


    public function destroy($fileName)
    {
        Storage::delete('ios-config-templates/' . $fileName);

        alert()->success("IOS configs removed successfully");

        return redirect()->back();
    }

    public function getModalDelete($fileName)
    {
        $error = null;

        $modal_title = trans('ios-config-generator/dialog.delete-confirm.title');
        $modal_cancel = trans('general.button.cancel');
        $modal_ok = trans('general.button.ok');

        $modal_route = route('ios-config-generator.delete', ['name' => $fileName]);

        $modal_body = trans('ios-config-generator/dialog.delete-confirm.body', ['name' => $fileName]);

        return view('modal_confirmation', compact('error', 'modal_route',
            'modal_title', 'modal_body', 'modal_cancel', 'modal_ok'));

    }

    public function download($fileName)
    {
        return response()->download(storage_path() . '/ios-config-templates/' .  $fileName);
    }
}
