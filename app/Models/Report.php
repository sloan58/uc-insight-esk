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
    protected $fillable = ['name','type','job'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Users');
    }
}
