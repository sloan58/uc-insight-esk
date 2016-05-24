<?php

namespace App\Libraries;

use \DuoAPI\Admin;


/**
 * Class DuoAdmin
 * @package App\Libraries
 */
class DuoAdmin extends Admin {


    /**
     *
     */
    function __construct()
    {
        parent::__construct(env('DUO_IKEY'),env('DUO_SKEY'),env('DUO_HOST'));
    }

    /*
     * Values a user/group's status can be set to. Note that this is what
     * they can be SET to, there are additional values that can be retrieved.
     */
    private static $SET_STATUS = array("active", "bypass", "disabled");

    private function is_status($status) {
        return is_string($status) && in_array($status, self::$SET_STATUS);
    }

    /**
     * @param $userid
     * @param $tokenid
     * @return mixed
     */
    public function user_associate_token($userid, $tokenid) {
        assert('is_string($userid)');
        assert('is_string($tokenid)');

        $method = "POST";
        $endpoint = "/admin/v1/users/" . $userid . "/tokens";
        $params = array(
            "token_id" => $tokenid,
        );

        return self::jsonApiCall($method, $endpoint, $params);
    }

    /**
     * @param $userid
     * @param $groupid
     * @return mixed
     */
    public function user_associate_group($userid, $groupid) {
        assert('is_string($userid)');
        assert('is_string($groupid)');

        $method = "POST";
        $endpoint = "/admin/v1/users/" . $userid . "/groups";
        $params = array(
            "group_id" => $groupid,
        );

        return self::jsonApiCall($method, $endpoint, $params);
    }

    public function create_user(
        $username,
        $realname = NULL,
        $email = NULL,
        $status = NULL,
        $notes = NULL) {
        assert('is_string($username)');
        assert('is_string($realname) || is_null($realname)');
        assert('is_string($email) || is_null($email)');
        assert('self::is_status($status) || is_null($status)');
        assert('is_string($notes) || is_null($notes)');

        $method = "POST";
        $endpoint = "/admin/v1/users";
        $params = array(
            "username" => $username,
        );

        if ($realname) {
            $params["realname"] = $realname;
        }
        if ($email) {
            $params["email"] = $email;
        }
        if ($status) {
            $params["status"] = $status;
        }
        if ($notes) {
            $params["notes"] = $notes;
        }

        return self::jsonApiCall($method, $endpoint, $params);
    }

    public function logs($mintime = NULL)
    {
        $method = "GET";
        $endpoint = "/admin/v1/logs/authentication";
        $params = array();

        if($mintime) {
            $endpoint .= ("/" . $mintime);
        }

        return self::jsonApiCall($method, $endpoint, $params);
    }
}