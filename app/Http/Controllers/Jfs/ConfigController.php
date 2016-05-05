<?php

namespace App\Http\Controllers\Jfs;

use Storage;
use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Libraries\UploadsManager;
use App\Http\Controllers\Controller;


/**
 * Class IosConfigGeneratorController
 * @package App\Http\Controllers
 */
class ConfigController extends Controller
{

    protected $manager;

    public function __construct(UploadsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $folder = $request->get('folder');

        if($folder == '') {
            $folder = 'jfs-config-templates/';
        }

        $data = $this->manager->folderInfo($folder);

        return view('jfs.configs.index', $data);
    }

    public function create(Request $request)
    {
        $fileNameAndPath = $request->input('path');

        //Get the file from storage
        $contents = Storage::get($fileNameAndPath);

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

        $filePath = explode('/', $fileNameAndPath);
        $fileName = end($filePath);

        return view('jfs.configs.create',compact('viewVariables','fileName'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function store(Request $request)
    {
        //Get the form input
        $input = $request->input();

        dd($input);

        //Get the file from storage
        $contents = Storage::get('jfs-config-templates/' .  $input['fileName']);

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

        $tempFileName = 'jfs-config-templates/temp/'. $input['fileName'] . '-' . 'completed' . '-' . Carbon::now()->timestamp . '.txt';

        Storage::put($tempFileName,$contents);

        return response()->download(storage_path() . '/' . $tempFileName)->deleteFileAfterSend(true);

    }

    public function loadFile(Request $request)
    {
        $file = $request->file('file');
        $folder = $request->input('folder');

        if ($file->getClientMimeType() != "text" && $file->getClientOriginalExtension() != "txt")
        {
            alert()->error('File type invalid.  Please use a .txt file format.');
            return redirect()->back();
        }

        $file->move(storage_path() . '/' . $folder . '/', $file->getClientOriginalName());

        alert()->success('New JFS Config Submitted!');
        return redirect()->back();
    }


    public function destroy(Request $request)
    {
        $fileNameAndPath = $request->input('name');

        Storage::delete($fileNameAndPath);

        alert()->success("JFS configs removed successfully");

        return redirect()->back();
    }

    public function getModalDelete(Request $request)
    {
        $fileNameAndPath = $request->input('path');

        $error = null;

        $modal_title = trans('jfs/dialog.delete-confirm.title');
        $modal_cancel = trans('general.button.cancel');
        $modal_ok = trans('general.button.ok');

        $modal_route = route('jfs.configs.delete', ['name' => $fileNameAndPath]);

        $modal_body = trans('jfs/dialog.delete-confirm.body', ['name' => $fileNameAndPath]);

        return view('modal_confirmation', compact('error', 'modal_route',
            'modal_title', 'modal_body', 'modal_cancel', 'modal_ok'));

    }

    public function download(Request $request)
    {
        return response()->download(storage_path() . '/' . $request->input('path'));
    }
}
