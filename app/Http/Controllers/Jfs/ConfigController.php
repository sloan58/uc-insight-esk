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
        // Get the folder name passed in the Request
        $folder = $request->get('folder');

        // If no folder was provided, we're at the root folder
        if($folder == '') {
            $folder = 'jfs-config-templates/';
        }

        // Get all subfolders
        $data = $this->manager->folderInfo($folder);

        // Remove the temp folder if it exists
        if(isset($data['subfolders']['/jfs-config-templates/temp'])) {
            unset($data['subfolders']['/jfs-config-templates/temp']);
        }
        
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

        $configSections = $begTags[0];

        //Loop each header
        foreach($configSections as $index => $tag) {

            if (count($configSections)) {
                //The headers are stored with the 'start' marker in
                //even indices.  If it's not even, it's an 'end' marker
                //and we can continue;
                if ($index % 2 != 0) continue;

                //Rip out { } from the headers to display nicely in HTML
                $viewHeader = trim(str_replace(['{', '}'], '', $tag));

                //Get the beginning position of the section
                $beginPos = strpos($contents, $tag);
                //Get the ending position of the section
                $endPos = strpos($contents, $configSections[$index + 1], $beginPos + 1);
                //Get the length between beginning and end
                $length = abs($beginPos - $endPos);

                //Extract the text between the header markers
                $between = substr($contents, $beginPos, $length);

                //Get an array of variable within the section
                preg_match_all('/<<.+?>>/', $between, $matches);
                $matches = array_unique($matches[0]);

            } else {

                $viewHeader = "Configs";

                //Get an array of variable within the section
                preg_match_all('/<<.+?>>/', $contents, $matches);
                $matches = array_unique($matches[0]);

            }

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

        return view('jfs.configs.create',compact('viewVariables', 'fileNameAndPath', 'fileName'));
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
        $contents = Storage::get($input['fileName']);

        //Find all the variables in the file
        preg_match_all('/<<.+?>>/',$contents,$matches);
        $matches = array_unique($matches[0]);

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
        // Get the file and folder submitted in the form
        $file = $request->file('file');
        $folder = $request->input('folder');

        // Check to see if the file type is correct
        if ($file->getClientMimeType() != "text" && $file->getClientOriginalExtension() != "txt")
        {
            alert()->error('File type invalid.  Please use a .txt file format.')->persistent('Close');
            return redirect()->back();
        }

        // Get all the variable names set in the config file.
        preg_match_all('/<<.+?>>/', file_get_contents($file), $matches);
        $variables = $matches[0];

        // Make sure each variable name conforms to standards
        foreach($variables as $variable) {
            if(preg_match('/\s/',$variable)) {
                alert()->error('Variable names cannot contain spaces.')->persistent('Close');
                return redirect()->back();
            }
        }

        // Move the file to persistent storage.
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
