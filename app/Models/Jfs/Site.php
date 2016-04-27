<?php

namespace App\Models\Jfs;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Site
 * @package App\Models\Jfs
 */
class Site extends Model
{
    /**
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workflows()
    {
        return $this->belongsToMany('Models\Jfs\Workflow');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tasks()
    {
        return $this->hasManyThrough('Models\Jfs\Task', 'Models\Jfs\Workflow');
    }
}
