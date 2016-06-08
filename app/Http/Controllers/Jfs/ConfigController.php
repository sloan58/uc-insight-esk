<?php

namespace App\Http\Controllers\Jfs;

use Storage;
use App\Http\Requests;
use App\Libraries\Jfs\ConfigGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;


/**
 * Class IosConfigGeneratorController
 * @package App\Http\Controllers
 */
class ConfigController extends Controller
{

    protected $configGeneratorService;

    /**
     * ConfigController constructor.
     * @param ConfigGeneratorService $configGeneratorService
     */
    public function __construct(ConfigGeneratorService $configGeneratorService)
    {
        $this->configGeneratorService = $configGeneratorService;
    }

    /**
     * Return a list of JFS files and/or folders
     *
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the folder name passed in the Request
        $folder = $request->get('folder');

        // Get the JFS folder data
        $data = $this->configGeneratorService->getJfsFolderData($folder);
        
        return view('jfs.configs.index', $data);
    }

    /**
     * Create the form variables with JFS config placeholders
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $fileNameAndPath = $request->input('path');

        //Get the file from storage
        $contents = Storage::get($fileNameAndPath);

        // Get the JFS Config Variables that will be returned
        $viewVariables = $this->configGeneratorService->getJfsViewVariables($contents);

        $filePath = explode('/', $fileNameAndPath);
        $fileName = end($filePath);

        return view('jfs.configs.create',compact('viewVariables', 'fileNameAndPath', 'fileName'));
    }

    /**
     *  Generate new JFS configs with variables submitted from the front end form.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function store(Request $request)
    {
        //Get the form input
        $input = $request->input();

        //Get the file from storage
        $contents = Storage::get($input['fileName']);

        // Get the temp file name for the JFS configs
        $tempFileName = $this->configGeneratorService->getJfsTempFileName($contents, $input);

        return response()->download(storage_path() . '/' . $tempFileName)->deleteFileAfterSend(true);

    }

    /**
     * Load a new JFS config file
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loadFile(Request $request)
    {
        // Get the file and folder submitted in the form
        $file = $request->file('file');
        $folder = $request->input('folder');

        // Make sure the file type and variable formatting is correct
        $this->configGeneratorService->validateJfsConfigData($file);
        
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
