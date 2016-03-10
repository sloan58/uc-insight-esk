<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 * @package App\Models
 */
class Report extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name','path','type','job','csv_headers'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Users');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function duoUsers()
    {
        return $this->belongsToMany('App\Models\Duo\User','duo_report_user','duo_user_id','report_id');
    }
}
