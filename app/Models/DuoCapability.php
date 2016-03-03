<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoCapability
 * @package App\Models
 */
class DuoCapability extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoPhones()
    {
        return $this->belongsToMany('App\Models\DuoPhone');
    }
}
