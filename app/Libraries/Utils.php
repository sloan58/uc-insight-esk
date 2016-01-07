<?php namespace App\Libraries;


class Utils {

    /**
     * @param $deviceList
     * @return mixed
     */
    public function generateEraserList($deviceList)
    {

        $macList = array_column($deviceList, 'mac');

        $axl = new AxlSoap();
        $user = $axl->getAxlUser();
        $devices = $this->createDeviceArray($user,$macList);
        $res = $axl->updateAxlUser($devices);
        $risArray = createRisPhoneArray($macList);

        // Get Device IP's
        $sxml = new RisSoap();
        $risResults = $sxml->getDeviceIP($risArray);
        $risPortResults = processRisResults($risResults,$risArray);

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
            $key = array_search($row['mac'], array_column($risPortResults, 'DeviceName'));
            $risPortResults[$key]['type'] = $row['type'];
            if(isset($row['bulk_id']))
            {
                $risPortResults[$key]['bulk_id'] = $row['bulk_id'];
            }
        }

        return $risPortResults;
    }

    /**
     * @param $userObj
     * @param $deviceList
     * @return array
     */
    private function createDeviceArray($userObj,$deviceList)
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
