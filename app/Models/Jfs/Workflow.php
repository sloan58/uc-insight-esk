<?php

namespace App\Models\Jfs;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeploymentWorkflow
 * @package App\Models\Jfs
 */
class Workflow extends Model
{
    /**
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sites()
    {
        return $this->belongsToMany('Models\Jfs\Site');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany('Models\Jfs\Task');
    }
}
