<?php

namespace App\Models;

use App\Libraries\AxlSoap;
use App\Exceptions\SqlQueryException;
use App\Models\Cluster as Cluster;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sql
 * @package App\Models
 */
class Sql extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['sqlhash', 'sql'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Users');
    }


    /**
     * @param $sql
     * @param Cluster $cluster
     * @return array
     * @throws \App\Exceptions\SqlQueryException
     */
    public function executeQuery($sql, Cluster $cluster)
    {
        $axl = new AxlSoap($cluster);

        $result = $axl->executeQuery($sql);

        switch($result) {

            case !isset($result->return->row):
                throw new SqlQueryException('No Results Found');
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
     * @param $data
     * @return bool|void
     */
    public function getHeaders($data)
    {
        if(isset($data[0]))
        {
            return $this->checkForUniqueSqlHeaders(array_keys((Array)$data[0]));
        } else {
            return false;
        }
    }

    /**
     * @param $sqlHeaders
     * @throws SqlQueryException
     * @return mixed
     */
    function checkForUniqueSqlHeaders($sqlHeaders)
    {
        /*
         * Check the first object property of $sqlHeaders
         * If there are duplicate column names, it will point
         * to an array, rather than a string.
         */
        if(is_array(current($sqlHeaders)))
        {
            Throw new SqlQueryException('Please use unique column names');
        }
        return $sqlHeaders;
    }
}
