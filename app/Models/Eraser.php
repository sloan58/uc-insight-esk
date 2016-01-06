<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Eraser
 * @package App\Models
 */
class Eraser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['device_id', 'device_description', 'ip_address_id', 'eraser_type', 'result', 'fail_reason'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ipAddress()
    {
        return $this->hasMany('App\Models\IpAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bulks()
    {
        return $this->belongsToMany('App\Models\Bulk');
    }
}
