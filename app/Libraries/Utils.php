<?php namespace App\Libraries;


/**
 * Class Utils
 * @package App\Libraries
 */
use App\Exceptions\SqlQueryException;
use App\Models\Cluster;

/**
 * Class Utils
 * @package App\Libraries
 */
class Utils {


    /**
     * @param $sql
     * @param Cluster $cluster
     * @return array
     * @throws SqlQueryException
     */
    public static function executeQuery($sql, Cluster $cluster)
    {
        $axl = new AxlSoap($cluster);

        $result = $axl->executeQuery($sql);

        switch($result) {

            case !isset($result->return->row):
                alert()->error('No Results Found')->persistent('Close');
                redirect()->back();
                break;

            case is_array($result->return->row):
                return $result->return->row;
                break;

            default:
                $return = [];
                $return[0] = $result->return->row;
                return $return;

        }
    }


    /**
     * @param $deviceList
     * @param Cluster $cluster
     * @return mixed
     */
    public static function generateEraserList($deviceList, Cluster $cluster)
    {
        $macList = array_column($deviceList, 'DeviceName');

        $axl = new AxlSoap($cluster);
        $user = $axl->getAxlUser();
        $devices = self::createDeviceArray($user,$macList);
        $axl->updateAxlUser($devices);

        // Get Device IP's
        $sxml = new RisSoap($cluster);
        $risArray = $sxml->createRisPhoneArray($macList);
        $risResults = $sxml->getDeviceIP($risArray);
        $risPortResults = $sxml->processRisResults($risResults,$risArray);

        //Fetch device model from type product
        for($i=0; $i<count($risPortResults); $i++)
        {
            if($risPortResults[$i]['IsRegistered'])
            {
                $results = $axl->executeQuery('SELECT name FROM typeproduct WHERE enum = "' . $risPortResults[$i]['Product'] . '"');
                $risPortResults[$i]['Model'] = $results->return->row->name;
            }
        }

        foreach($deviceList as $row)
        {
            // Merge the $deviceList and $risPortResults arrays
            $key = array_search($row['DeviceName'], array_column($risPortResults, 'DeviceName'));
            $mergedDeviceArray[] = array_merge($row,$risPortResults[$key]);
        }

        return $mergedDeviceArray;
    }

    /**
     * @param $userObj
     * @param $deviceList
     * @return array
     */
    private static function createDeviceArray($userObj,$deviceList)
    {
        //$device array to be returned
        $devices = [];

        /*
         * Check if UserObj deviceList is not set.
         * (user is not currently associated to any devices)
         */
        if (!isset($userObj->return->appUser->associatedDevices->device))
        {

            /*
             * Add all device from the device list
             * to the devices array
             */
            foreach($deviceList as $d)
            {
                $devices[] = $d;
            }
        }
        /*
         * Check if userObj devices is an array
         * (user is associated to multiple devices)
         */
        elseif (is_array($userObj->return->appUser->associatedDevices->device)) {

            $devices = array_merge($userObj->return->appUser->associatedDevices->device,$deviceList);

            //If the userObj element DOES exist but IS NOT and array (it's a single device)
            /*
             * The userObj devices object exists
             * but is not an array.
             * (it's a single device)
             */
        } else {

            array_push($deviceList,$userObj->return->appUser->associatedDevices->device);
            $devices = $deviceList;
        }

        /*
         * It's possible some devices were
         * already associated to the appUser.
         * Here we make sure the device names
         * are unique.
         */
        $devices = array_unique($devices);

        /*
         * We only want to return the
         * array values.
         * (Can't remember why I had to do this...)
         */
        $devices = array_values($devices);


        return $devices;
    }
}
