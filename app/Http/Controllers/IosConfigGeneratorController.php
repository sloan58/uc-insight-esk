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
     * Return a list of Config files
     *
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
     * Create the config file form
     *
     * @param $fileName
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function create($fileName)
    {
        //Get the config file from storage
        $contents = Storage::get('ios-config-templates/' .  $fileName);

        //Create an XML reader and read in the XML file
        $reader = new \Sabre\Xml\Reader();
        $reader->xml($contents);
        $result = $reader->parse();

        //Loop each node in the XML document under the main <config> element
        $sections = [];
        foreach($result['value'] as $index => $xmlNode)
        {
            //Remove { } from the XML parser output
            $header = str_replace(['{','}'],'',$xmlNode['name']);
            //Replace XML node hyphens with spaces
            $header = str_replace('-',' ',$header);
            //Capilalize the first letter of each word
            $header = ucwords($header);

            //Get the body content of the config and remove tabs
            $body = $xmlNode['value'];
            $body = trim(preg_replace('/\t/','',$body));

            //Find all the variables inside the config section
            preg_match_all('/{{.+?}}/',$body,$matches);
            $matches = $matches[0];

            //Place the variables into an array
            //for use in the view.
            $viewVariables = [];
            foreach($matches as $match)
            {
                preg_match('/{{(.*)}}/',$match,$out);
                $viewVariables[] = $out;

            }

            //Put the XML header and vars into array elements
            $sections[$index]['Header'] = $header;
            $sections[$index]['Vars'] = $viewVariables;
        }

        //Return the view with vars and the filename
        return view('ios-config-generator.create',compact('sections','fileName'));
    }

    /**
     * Process the submitted config form and
     * save the results to a file then ship
     * the file to the view.  Delete file afterward.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function store(Request $request)
    {
        //Get form input
        $input = $request->input();
        //Open the file from storage
        $contents = Storage::get('ios-config-templates/' .  $input['fileName']);

        //Pull out all variable names from the open file
        preg_match_all('/{{.+?}}/',$contents,$matches);
        $matches = $matches[0];

        //Loop through the variables defined in the source config file
        //and replace them with the data submitted in the form
        foreach($matches as $match)
        {
            $contents = str_replace($match,$input[$match],$contents);
        }

        //Creaete a new XML reader and read in the config
        //file with variables converted
        $reader = new \Sabre\Xml\Reader();
        $reader->xml($contents);
        $result = $reader->parse();

        //Loop each node in the XML file and
        //place the Body in a flat config text file
        $outFile = '';
        foreach($result['value'] as $index => $xmlNode)
        {
            $outFile .= trim(preg_replace('/\t/','',$xmlNode['value'])) .  "\n";
        }

        //Give the new tempFile and name
        $tempFileName = 'ios-config-templates/temp/'. $input['fileName'] . '-' . 'completed' . '-' . \Carbon\Carbon::now()->timestamp . '.txt';

        //Store the file on disk
        Storage::put($tempFileName,$outFile);

        //Return the file, then delete it
        return response()->download(storage_path() . '/' . $tempFileName)->deleteFileAfterSend(true);

    }

    /**
     * Load a new config file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loadFile(Request $request)
    {
        //Get the input file
        $file = $request->file('file');

        //Check the file type
        if ($file->getClientMimeType() != "text" && $file->getClientOriginalExtension() != "xml")
        {
            //Invalid file type
            alert()->error('File type invalid.  Please use a .txt file format.');
            return redirect()->back();
        }

        //Move the file to disk
        $file->move(storage_path() . '/ios-config-templates/', $file->getClientOriginalName());

        //Return success
        alert()->success('New IOS Config Submitted!');
        return redirect()->back();
    }


    /**
     * Delete a config file
     *
     * @param $fileName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($fileName)
    {
        //Remove the submitted file from disk
        Storage::delete('ios-config-templates/' . $fileName);

        //Return success
        alert()->success("IOS configs removed successfully");
        return redirect()->back();
    }

    /**
     * @param $fileName
     * @return \BladeView|bool|\Illuminate\View\View
     */
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

    /**
     * Process a file download request
     *
     * @param $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($fileName)
    {
        return response()->download(storage_path() . '/ios-config-templates/' .  $fileName);
    }
}
