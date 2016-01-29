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
