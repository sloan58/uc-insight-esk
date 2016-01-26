<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Bulk
 * @package App\Models
 */
class Bulk extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['file_name'];
    protected $with = ['erasers'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function erasers()
    {
        return $this->belongsToMany('App\Models\Eraser');
    }
}
