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


        //Create the reporting Excel Object
        $objPHPExcel = new PHPExcel();

        //Get all groups that the report subscriber belongs to
        $groups = \App\Models\Duo\User::where('username','LIKE','%Pavol%')->first()->duoGroups()->get();

        //Loop each Duo Group
        foreach($groups as $group)
        {
            //Get our Report object
            $duoReport = \App\Models\Report::where('name', 'DuoRegisteredUsersReport')->first();

            //Explode csv_headers to array
            $duoReportHeaders = explode(',',$duoReport->csv_headers);

            // Create a new worksheet using the Duo Group namer
            $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, $group->name);

            // Attach the worksheet to the workbook
            $objPHPExcel->addSheet($myWorkSheet);

            //Set the active sheet
            $objPHPExcel->setActiveSheetIndexByName($group->name);

            //Get all users that belong to this group
            $users = $group->duoUsers()->get();

            //Write the CSV header information
            for ($i=0; $i<count($duoReportHeaders);$i++)
            {
                $column = PHPExcel_Cell::stringFromColumnIndex($i);

                // Set cell A1 with a string value
                $objPHPExcel->getActiveSheet()->setCellValue($column . '1', $duoReportHeaders[$i]);
            }

            //Write user data
            $row = 2;
            foreach($users as $user)
            {

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row, $user->username);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row, $user->email);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row, $user->status);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row, $user->last_login);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row, $user->duoGroups()->first()->name);

                $row++;
            }
        }

        //Remove the default sheet (there's gotta be a better way to do this....)
        $objPHPExcel->removeSheetByIndex(0);

        //Write the document
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(storage_path() . "/ExcelTest.xlsx");

    }
}
