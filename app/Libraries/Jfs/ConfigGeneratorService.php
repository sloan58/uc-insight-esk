<?php namespace App\Libraries\Jfs;

use Storage;
use Carbon\Carbon;
use App\Libraries\UploadsManager;

class ConfigGeneratorService
{
    protected $manager;

    /**
     * ConfigGeneratorService constructor.
     * @param UploadsManager $manager
     */
    public function __construct(UploadsManager $manager)
    {
        $this->manager = $manager;

    }

    /**
     * Produce the JFS config variables from the source file
     *
     * @param $contents
     * @return array
     */
    public function getJfsViewVariables($contents) {
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

        return $viewVariables;
    }

    /**
     * Get the folder data from disk
     *
     * @param $folder
     * @return array
     */
    public function getJfsFolderData($folder) {
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

        return $data;
    }

    public function getJfsTempFileName($contents, $input) {

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

        // Create the temp file name
        $tempFileName = 'jfs-config-templates/temp/'. $input['fileName'] . '-' . 'completed' . '-' . Carbon::now()->timestamp . '.txt';

        // Save the file to disk
        Storage::put($tempFileName,$contents);

        return $tempFileName;
    }

    /**
     * Validate JFS config file type and the variables formatting
     *
     * @param $file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateJfsConfigData($file) {

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
    }
}