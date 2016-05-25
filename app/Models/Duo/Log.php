<?php

namespace App\Models\Duo;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    /**
     *  The database table name
     */
    protected $table = 'duo_logs';

    /**
     *  Table fields that should be used as Carbon objects
     */
    protected $dates = ['timestamp'];

    /**
     *  Mass assignable fields
     */
    protected $fillable = ['device', 'factor', 'integration', 'ip', 'new_enrollment', 'reason', 'result', 'timestamp', 'username'];

    /**
     *  Relations to include
     */
    protected $with =['duoUser'];

    /**
     *  Duo Logs belong to a Duo User
     */
    public function duoUser()
    {
        return $this->belongsTo('App\Models\Duo\User');
    }
    
    /**
     *  Check if the new_enrollment value is true
     *  Return Yes or No
     */
    public function getNewEnrollmentAttribute($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
