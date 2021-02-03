<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class switche extends Model
{
    protected $guarded = [];

    protected $seachable = ['name', 'device_id', 'room_id', 'apartment_id'];
    /**
     * Get the user that owns the device.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device()
    {
        return $this->belongsTo(device::class, 'device_id', 'id');
    }
    /**
     * Get the user that owns the device.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Get the user that owns the device.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type_device()
    {
        return $this->belongsTo(type_device::class, 'type_id', 'id');
    }
    /**
     * Scope a query to only include users with the given device_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $device_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByDeviceId($query, $device_id)
    {
        return $query->where('device_id', '=', $device_id);
    }
     /**
     * Scope a query to only include users with the given user_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $user_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByUserId($query, $user_id)
    {
        return $query->where('user_id', '=', $user_id);
    }
     /**
     * Scope a query to only include users with the given status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query)
    {
        return $query->where('status', 1);
    }
}
