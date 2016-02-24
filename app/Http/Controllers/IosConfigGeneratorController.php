<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Storage;

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
        $files = Storage::files('app/ios-config-templates/');

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
        $contents = Storage::get('app/ios-config-templates/' .  $fileName);

        preg_match_all('/<<.+?>>/',$contents,$matches);

        $matches = $matches[0];

        $viewVariables = [];
        foreach($matches as $match)
        {
            preg_match('/<<(.*)>>/',$match,$out);
            $viewVariables[] = $out;

        }

        return view('ios-config-generator.create',compact('viewVariables','fileName'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function store(Request $request)
    {
        $input = $request->input();
        $contents = Storage::get('app/ios-config-templates/' .  $input['fileName']);

        preg_match_all('/<<.+?>>/',$contents,$matches);

        $matches = $matches[0];

        foreach($matches as $match)
        {
            $contents = str_replace($match,$input[$match],$contents);
        }

        $tempFileName = 'app/ios-config-templates/temp/'. $input['fileName'] . '-' . 'completed' . '-' . \Carbon\Carbon::now()->timestamp . '.txt';

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

        $file->move(storage_path() . '/app/ios-config-templates/', $file->getClientOriginalName());

        alert()->success('New IOS Config Submitted!');
        return redirect()->back();
    }


    public function destroy($fileName)
    {
        Storage::delete('app/ios-config-templates/' . $fileName);

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
        return response()->download(storage_path() . '/app/ios-config-templates/' .  $fileName);
    }
}
