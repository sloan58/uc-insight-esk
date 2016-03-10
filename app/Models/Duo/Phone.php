<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoPhone
 * @package App\Models
 */
class Phone extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['phone_id','name','number','platform','extension','activated','postdelay','predelay','sms_passcodes_sent','type','duo_users_id'];

    /**
     * @var string
     */
    protected $table = 'duo_phones';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoCapabilities()
    {
        return $this->belongsToMany('App\Models\Duo\Capability','duo_capability_duo_phone','duo_phone_id','duo_capability_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Model\Duo\User','duo_phone_duo_user','duo_phone_id','duo_user_id');
    }
}
