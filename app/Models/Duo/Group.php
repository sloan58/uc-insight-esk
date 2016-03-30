<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoGroup
 * @package App\Models
 */
class Group extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['group_id'];

    /**
     * @var string
     */
    protected $table = 'duo_groups';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Models\Duo\User', 'duo_group_duo_user','duo_group_id','duo_user_id')->withPivot('duo_assigned');
    }

}
