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
    protected $fillable = ['name', 'model', 'protocol'];

    /**
     * A Device can run many Eraser try's
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function Eraser()
    {
        return $this->hasMany('App\Models\Device');
    }

    /**
     * A Device can be known by many IP Addresses
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ipAddress()
    {
        return $this->hasMany('App\Models\IpAddress');
    }
}
