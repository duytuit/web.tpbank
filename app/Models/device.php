<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class device extends Model
{
    protected $guarded = [];
    /**
     * Get the user that owns the apartment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(room::class,'room_id', 'id');
    }
    /**
     * Get the user that owns the apartment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
         return $this->belongsTo(User::class, 'user_id', 'id');
    }
    /**
     * Scope a query to only include users with the given room_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $room_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRoomId($query, $room_id)
    {
        return $query->where('room_id', '=', $room_id);
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
