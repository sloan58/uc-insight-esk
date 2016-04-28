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
     * @var string
     */
    protected $table = 'jfs_workflows';

    /**
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sites()
    {
        return $this->belongsToMany('App\Models\Jfs\Site', 'jfs_workflow_jfs_site', 'jfs_workflow_id', 'jfs_site_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->hasMany('App\Models\Jfs\Task', 'jfs_workflow_id');
    }
}
