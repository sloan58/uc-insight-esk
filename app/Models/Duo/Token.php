<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Token
 * @package App\Models\Duo
 */
class Token extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['serial','token_id','type','totp_step'];

    /**
     * @var string
     */
    protected $table = 'duo_tokens';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Models\Duo\User','duo_token_duo_user','duo_token_id','duo_user_id');
    }
}
