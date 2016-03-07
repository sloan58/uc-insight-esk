<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuoToken extends Model
{
    protected $fillable = ['serial','token_id','type','totp_step'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Models\DuoUser');
    }
}
