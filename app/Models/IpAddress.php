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
    protected $fillable = ['ip_address'];


    /**
     *  An IP Address belongs to many Devices
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function devices()
    {
        return $this->belongsToMany('App\Models\Device');
    }

}
