<?php

/*
|--------------------------------------------------------------------------
| Detect Active Route
|--------------------------------------------------------------------------
|
| Compare given route with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/

/*
|--------------------------------------------------------------------------
| Detect Active Routes
|--------------------------------------------------------------------------
|
| Compare given routes with current route and return output if they match.
| Very useful for navigation, marking if the link is active.
|
*/
/**
 * @param array $routes
 * @param string $output
 * @return string
 */
function areActiveRoutes(Array $routes, $output = "active")
{
    foreach ($routes as $route)
    {
        if (Route::currentRouteName() == $route) return $output;
    }

}
/**
 * @param $route
 * @param string $output
 * @return string
 */
function isActiveRoute($route, $output = "active")
{
    if (Route::currentRouteName() == $route) return $output;
}

/*
|--------------------------------------------------------------------------
| Check Passwords
|--------------------------------------------------------------------------
|
| Compare passwords to see if they need to be updated in the database.
|
*/
function checkPassword($currentPassword,$toCheckPassword)
{
    if($currentPassword != $toCheckPassword && $toCheckPassword != '')
    {

        return $toCheckPassword;

    } else {

        return $currentPassword;
    }
}

/**
 * @param $model
 * @param $tleType
 * @internal param $logger
 * @return array
 */
function setKeys($model,$tleType)
{
    switch(strtolower($tleType)) {

        case 'itl':

            switch ($model) {

                case "Cisco 7905":
                case "Cisco 7906":
                case "Cisco 7911":
                    return [
                        'Init:Applications',
                        'Key:Applications',
                        'Key:KeyPad3',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad2',
                        'Key:Soft2',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Init:Applications',
                    ];
                    break;

                case "Cisco 7941":
                case "Cisco 7942":
                case "Cisco 7961":
                case "Cisco 7962":
                    return [
                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad1',
                        'Key:KeyPad2',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Init:Services'
                    ];
                    break;
                case "Cisco 7945":
                case "Cisco 7965":
                    return [

                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad2',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Init:Services'
                    ];
                    break;
                case "Cisco 7971":
                case "Cisco 7975":
                    return [

                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad2',
                        'Key:Soft5',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft5',
                        'Init:Services'
                    ];
                    break;

                case "Cisco 8945":
                    return [
                        'Key:NavBack',
                        'Key:Sleep',
                        'Key:NavBack',
                        'Key:Sleep',
                        'Key:NavBack',
                        'Key:Sleep',
                        'Key:NavBack',
                        'Key:Sleep',
                        'Key:NavBack',
                        'Key:Sleep',
                        'Key:Settings',
                        'Key:Sleep',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:KeyPad3',
                        'Key:Sleep',
                        'Key:Soft3',
                    ];
                    break;

                case "Cisco 8961":
                case "Cisco 9951":
                case "Cisco 9971":
                    return [

                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:Applications',
                        'Key:KeyPad4',
                        'Key:KeyPad4',
                        'Key:KeyPad4',
                        'Key:Soft3',
                    ];
                    break;

                case "Cisco 8831":  //8800's interface is lazy, so we need to pause between each key press.
                    return [
                        'Key:Soft3',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Sleep',
                        'Key:Soft2',
                    ];
                    break;

                case "Cisco 8841":  //8800's interface is lazy, so we need to pause between each key press.
                    return [
                        'Init:Settings',
                        'Key:Sleep',
                        'Key:Settings',
                        'Key:Sleep',
                        'Key:KeyPad5',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:Soft3',
                    ];
                    break;

                case "Cisco 8851":  //8800's interface is lazy, so we need to pause between each key press.
                case "Cisco 8861":
                    return [
                        'Init:Settings',
                        'Key:Sleep',
                        'Key:Settings',
                        'Key:Sleep',
                        'Key:KeyPad6',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:Soft3',
                    ];
                    break;

                case "Cisco 7821":
                case "Cisco 7841":
                case "Cisco 7861":
                    return [
                        'Init:Settings',
                        'Key:Sleep',
                        'Key:Settings',
                        'Key:Sleep',
                        'Key:KeyPad5',
                        'Key:Sleep',
                        'Key:KeyPad4',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Sleep',
                        'Key:Soft2',
                    ];
                    break;

                default:
                    Log::error("ITL-> No model found for " . $model);
                    return false;
            }
            break;

        case 'ctl':

            switch ($model) {
                case "Cisco 7905":
                case "Cisco 7906":
                case "Cisco 7911":
                    return [
                        'Init:Applications',
                        'Key:Applications',
                        'Key:KeyPad3',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad1',
                        'Key:Soft2',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Key:Soft2',
                        'Init:Applications',
                    ];
                    break;
                case "Cisco 7941":
                case "Cisco 7942":
                case "Cisco 7945":
                case "Cisco 7961":
                case "Cisco 7965":
                    return [
                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad1',
                        'Key:Soft4',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft4',
                        'Init:Services'
                    ];
                    break;
                case "Cisco 7971":
                case "Cisco 7975":
                    return [
                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad4',
                        'Key:KeyPad5',
                        'Key:KeyPad1',
                        'Key:Soft5',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft5',
                        'Init:Services'
                    ];
                    break;
                case "Cisco IP Communicator":
                    return [
                        'Init:Settings',
                        'Key:Settings',
                        'Key:KeyPad5',
                        'Key:KeyPad4',
                        'Key:KeyPad1',
                        'Key:Soft5',
                        'Key:Sleep',
                        'Key:KeyPadStar',
                        'Key:KeyPadStar',
                        'Key:KeyPadPound',
                        'Key:Sleep',
                        'Key:Soft5',
                        'Init:Services'
                    ];
                    break;
                case "Cisco 8961":
                case "Cisco 9951":
                case "Cisco 7937":
                case "Cisco 9971":
                    return [
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:NavBack',
                        'Key:Applications',
                        'Key:KeyPad4',
                        'Key:KeyPad4',
                        'Key:KeyPad4',
                        'Key:Soft3',
                    ];
                    break;
                default:
                    Log::error("CTL-> No model found for " . $model);
                    return false;
            }
            break;

    }
}

function searchMultiDimArray($array, $field, $value)
{
    foreach($array as $key => $product)
    {
        if ( $product[$field] === $value )
            return $key;
    }
    return false;
}

/*
 * Uploads Manager helpers
 */
/**
 * Return sizes readable by humans
 */
function human_filesize($bytes, $decimals = 2)
{
    $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .
    @$size[$factor];
}

/**
 * Is the mime type an image
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}