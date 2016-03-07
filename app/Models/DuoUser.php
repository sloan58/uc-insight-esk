<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoUser
 * @package App\Models
 */
class DuoUser extends Model
{
    /**
     * @var array
     */
    protected $dates = ['last_login'];

    /**
     * @var array
     */
    protected $fillable = ['user_id','username','status','realname','notes','last_login','email'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoGroups()
    {
        return $this->belongsToMany('App\Models\DuoGroup');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoPhones()
    {
        return $this->belongsToMany('App\Models\DuoPhone');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoTokens()
    {
        return $this->belongsToMany('App\Models\DuoToken');
    }

}
