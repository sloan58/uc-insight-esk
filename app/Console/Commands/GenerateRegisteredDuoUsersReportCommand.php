<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Report;
use App\Models\Cluster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Worksheet;
use PHPExcel_Writer_Excel2007;

class GenerateRegisteredDuoUsersReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duo:user-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the Duo API User and Group Data';

    /**
     * Create a new command instance.
     *
     * @return \App\Console\Commands\FetchDuoApiDataCommand
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        \Debugbar::disable();

        //Temp hard coding of report recipients
        $users = [
            'Fadi Tahan',
            'pavol popovic',
            'Jeanne Guilhas',
            'Brian Shields',
            'Ryan Means'
        ];

        //Loop each user to generate report
        foreach($users as $user)
        {
            //Create the reporting Excel Object
            $objPHPExcel = new PHPExcel();

            //Get the report recipient
            $recipient = \App\Models\Duo\User::where('username',$user)->first();

            //Check if the recipient exists.  If not, log and continue.
            if(!$recipient)
            {
                \Log::debug('Report recipient ' . $user . ' not found:',[$user]);
                continue;
            }

            //Check if the recipient is assigned to a group.  If not, log and continue.
            if($recipient->duoGroups()->count())
            {
                $groups = $recipient->duoGroups()->get();
            } else {
                \Log::debug($recipient->username . ' has no groups:',[$recipient]);
                continue;
            }

            //Set timestamp for file names
            $timeStamp = Carbon::now('America/New_York')->toDateTimeString();

            //Get our Report object
            $duoReport = \App\Models\Report::where('name', 'DuoRegisteredUsersReport')->first();

            $fileName = storage_path() . '/' . $duoReport->path . $timeStamp . '-' . $duoReport->name . '-' . $recipient->username .'.xlsx';

            //Loop each Duo Group
            foreach($groups as $group)
            {

                //Explode csv_headers to array
                $duoReportHeaders = explode(',',$duoReport->csv_headers);

                // Create a new worksheet using the Duo Group namer
                $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $group->name);

                // Attach the worksheet to the workbook
                $objPHPExcel->addSheet($myWorkSheet);

                //Set the active sheet
                $objPHPExcel->setActiveSheetIndexByName($group->name);

                //Get all users that belong to this group
                $duoGroupMembers = $group->duoUsers()->get();

                //Write the CSV header information
                for ($i=0; $i<count($duoReportHeaders);$i++)
                {
                    $column = PHPExcel_Cell::stringFromColumnIndex($i);

                    // Set cell A1 with a string value
                    $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $duoReportHeaders[$i]);
                }

                //Write user data
                $row = 2;
                foreach($duoGroupMembers as $member)
                {
                    //Check if the user has a registered phone or token
                    if($member->duoPhones()->count() || $member->duoTokens()->count())
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $member->username);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $member->email);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $member->status);
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $member->last_login);
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $member->duoGroups()->first()->name);

                        $row++;
                    }
                }
            }

            //Remove the default sheet (there's gotta be a better way to do this....)
            $objPHPExcel->removeSheetByIndex(0);

            //Write the document
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save($fileName);

            //Reports are done running, let's email to results
            $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
            $beautymail->send('emails.duo-registered-users', [], function($message) use($fileName,$recipient)
            {
                //TODO: Create system for users to manage report subscriptions.
                $message
                    ->from('duo_reports@ao.uscourts.gov','Duo Reporting')
                    ->to(['martin_sloan@ao.uscourts.gov','fadi_tahan@ao.uscourts.gov'])
//                    ->to($recipient->email)
//                    ->cc('martin_sloan@ao.uscourts.gov')
                    ->subject('Duo Registered Users Report')
                    ->attach($fileName);

            });

            \Log::debug('Message will be sent to:',[$recipient->email]);

        }
    }
}
