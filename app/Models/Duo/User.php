<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoUser
 * @package App\Models
 */
class User extends Model
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
     * @var string
     */
    protected $table = 'duo_users';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoGroups()
    {
        return $this->belongsToMany('App\Models\Duo\Group','duo_group_duo_user','duo_user_id','duo_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoPhones()
    {
        return $this->belongsToMany('App\Models\Duo\Phone','duo_phone_duo_user','duo_user_id','duo_phone_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoTokens()
    {
        return $this->belongsToMany('App\Models\Duo\Token','duo_token_duo_user','duo_user_id','duo_token_id');
    }

}
