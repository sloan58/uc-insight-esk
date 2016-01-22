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
    protected $fillable = ['device_id', 'ip_address_id', 'type', 'result', 'fail_reason'];

    protected $with = ['ipAddress'];

    /**
     * An Eraser belongs to a Device
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }

    /**
     * An Eraser can have many IP Addresses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ipAddress()
    {
        return $this->belongsTo('App\Models\IpAddress');
    }

    /**
     *  An Eraser belongs to many Bulk processes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bulks()
    {
        return $this->belongsToMany('App\Models\Bulk');
    }
}
