<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DuoCapability
 * @package App\Models
 */
class Capability extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @var string
     */
    protected $table = 'duo_capabilities';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoPhones()
    {
        return $this->belongsToMany('App\Models\Duo\Phone','duo_capability_duo_phone','duo_capability_id','duo_phone_id');
    }
}
