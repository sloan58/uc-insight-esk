<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoPhone
 * @package App\Models
 */
class DuoPhone extends Model
{
    protected $fillable = ['phone_id','name','number','platform','extension','activated','postdelay','predelay','sms_passcodes_sent','type','duo_users_id'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoCapabilities()
    {
        return $this->belongsToMany('App\Models\DuoCapability');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Model\User');
    }
}
