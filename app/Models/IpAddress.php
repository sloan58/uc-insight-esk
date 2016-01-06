<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Eraser
 * @package App\Models
 */
class IpAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['device_id', 'ip_address', 'eraser_type', 'result'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bulks()
    {
        return $this->belongsToMany('App\Models\Bulk');
    }
}
