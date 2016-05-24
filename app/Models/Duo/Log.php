<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    /**
     * @var string
     */
    protected $table = 'duo_logs';

    protected $fillable = ['device', 'factor', 'integration', 'ip', 'new_enrollment', 'reason', 'result', 'timestamp', 'username'];

    public function duoUsers()
    {
        return $this->belongsTo('App\Models\Duo\User');
    }
}
