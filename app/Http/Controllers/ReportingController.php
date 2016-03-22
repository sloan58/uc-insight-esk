<?php

namespace App\Http\Controllers;


use App\Models\Report;
use App\Repositories\Criteria\Duo\UserWhereUsernameOrRealnameLike;
use Carbon\Carbon;
use App\Http\Requests;
use DB;
use Keboola\Csv\CsvFile;
use App\Libraries\Utils;
use Illuminate\Http\Request;
use App\Jobs\GetPhoneFirmware;
use App\Libraries\ControlCenterSoap;
use App\Http\Controllers\Controller;
use App\Models\Duo\User as DuoUser;
use Storage;

/**
 * Class ReportingController
 * @package App\Http\Controllers
 */
class ReportingController extends Controller
{

    /**
     * @var \App\Models\Duo\User
     */
    private $duoUser;

    /**
     * @param DuoUser $duoUser
     */
    function __construct(DuoUser $duoUser)
    {
        $this->duoUser = $duoUser;
    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function servicesIndex()
    {
        //Get the active cluster for the logged in user
        $cluster = \Auth::user()->activeCluster();

        //Set the SQL query based on the CUCM DB version
        switch($cluster->version)
        {
            case (preg_match('/10/', $cluster->version) ? true : false):
                $data = Utils::executeQuery('SELECT name FROM processnode WHERE tkprocessnoderole = 1 AND name != "EnterpriseWideData"',$cluster);
                break;
            case (preg_match('/9/', $cluster->version) ? true : false):
                $data = Utils::executeQuery('SELECT name FROM processnode WHERE name != "EnterpriseWideData"',$cluster);
                break;
        }

        //Loop each node in the cluster to get all services status
        $clusterStatus = [];
        foreach($data as $node)
        {
            $ris = new ControlCenterSoap($cluster,$node->name);
            $clusterStatus[$node->name] = $ris->getServiceStatus();
        }

        return view('reports.services.show', compact('clusterStatus'));
    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function registrationIndex()
    {
        // Avoid PHP timeouts when querying large clusters
        set_time_limit(0);

        $cluster = \Auth::user()->activeCluster();

        // Query CUCM for device name and model
        $data = Utils::executeQuery('SELECT d.name devicename, t.name model FROM device d INNER JOIN typemodel t ON d.tkmodel = t.enum',$cluster);

        // $deviceList will hold our array for RisPort
        $deviceList = [];

        // Loop SQL data and assign devicename to $deviceList
        foreach($data as $key => $val)
        {
            $deviceList[$key]['DeviceName'] = $val->devicename;
        }

        $registrationReport = \App\Libraries\Utils::generateEraserList($deviceList,$cluster);

        return view('reports.registration.show', compact('registrationReport'));

    }

    /**
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function firmwareIndex()
    {
        return view('reports.firmware.index');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function firmwareStore(Request $request)
    {
        // Avoid PHP timeouts when querying large clusters
        set_time_limit(0);

        //Get the authenticated users active cluster
        $cluster = \Auth::user()->activeCluster();


        //Get the file submitted from the form
        $file = $request->file('file');

        //Make sure the file is a csv and redirect back if it's not
        if ($file->getClientMimeType() != "text/csv" && $file->getClientOriginalExtension() != "csv")
        {
            alert()->error('File type invalid.  Please use a CSV file format.');
            return redirect()->back();
        }

        //Create new Keboola\Csv Object with the submitted file
        $csvFile = new CsvFile($file);
        $csv = '';

        //Loop the csv file and store the device names in an array
        foreach($csvFile as $key => $row)
        {
            $csv[] = $row[0];
        }

        // $deviceList will hold our array for RisPort
        $deviceList = [];

        // Loop device name array and assign devicename to $deviceList
        foreach($csv as $phone)
        {
            $deviceList[]['DeviceName'] = $phone;
        }

        //Query the RisPort API to get IP/Registration status
        $devices = Utils::generateEraserList($deviceList,$cluster,false);

        //Get details for the phone_firmware report type
        $report = Report::where('type','phone_firmware')->first();

        //Create file name for report
        $fileName = storage_path() . '/' . $report->path . 'firmware-report-' . Carbon::now('America/New_York')->toDateTimeString() . '.csv';

        //Generate new output csv file
        new CsvFile($fileName);

        //Call the App\Jobs\GetPhoneFirmware Job
        $this->dispatch(new $report->job($devices,$fileName,$report->csv_headers));

        //Return a response with the firmware report
        return response()->download($fileName);
    }

}
