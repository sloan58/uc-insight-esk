<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Storage;

class IosConfigGeneratorController extends Controller
{
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
}
