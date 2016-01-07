<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Cluster
 * @package App\Models
 */
class Cluster extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'ip', 'username', 'password', 'version', 'verify_peer', 'user_type'];

    /**
     * A Cluster can be assigned to many Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }
}

