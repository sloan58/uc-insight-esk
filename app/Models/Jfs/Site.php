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
     * @var string
     */
    protected $table = 'jfs_sites';

    /**
     * @var array
     */
    protected $fillable = [ 'name' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workflows()
    {
        return $this->belongsToMany('App\Models\Jfs\Workflow', 'jfs_workflow_jfs_site', 'jfs_site_id', 'jfs_workflow_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany('App\Models\Jfs\Task', 'jfs_site_jfs_task', 'jfs_site_id', 'jfs_task_id')->withPivot('completed');
    }

    /**
     * Return a list of incomplete tasks for this site
     * @param bool $getCount
     * @return mixed
     */
    public function incompleteTasks($getCount = false)
    {
        if($getCount) {
            return count($this->tasks()->where('completed',0)->get());
        }
        return $this->tasks()->where('completed',0)->get();
    }

    /**
     * Return a list of completed tasks for this site
     * @param bool $getCount
     * @return mixed
     */
    public function completedTasks($getCount = false)
    {
        if($getCount) {
            return count($this->tasks()->where('completed',1)->get());
        }
        return $this->tasks()->where('completed',1)->get();
    }
}
