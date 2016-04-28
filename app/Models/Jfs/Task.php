<?php

namespace App\Models\Jfs;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Task
 * @package App\Models\Jfs
 */
class Task extends Model
{
    /**
     * @var string
     */
    protected $table = 'jfs_tasks';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workflow()
    {
        return $this->belongsTo('App\Models\Jfs\Workflow', 'jfs_workflow_id');
    }

    /**
     * @return $this
     */
    public function sites()
    {
        return $this->belongsToMany('App\Models\Jfs\Site', 'jfs_site_jfs_task', 'jfs_task_id', 'jfs_site_id')->withPivot('completed');
    }
}
