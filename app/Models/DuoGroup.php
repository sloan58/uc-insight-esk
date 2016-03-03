<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoGroup
 * @package App\Models
 */
class DuoGroup extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['group_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Models\DuoUser');
    }

}
