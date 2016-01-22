<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Device
 * @package App\Models
 */
class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'model', 'protocol'];

    /**
     * A Device can run many Eraser try's
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function erasers()
    {
        return $this->hasMany('App\Models\Eraser');
    }

    /**
     * Return the latest Eraser Job
     * @return mixed
     */
    public function latestEraser()
    {
        return $this->hasOne('App\Models\Eraser')->latest('updated_at');
    }

    /**
     * A Device can be known by many IP Addresses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ipAddresses()
    {
        return $this->belongsToMany('App\Models\IpAddress');
    }

}
