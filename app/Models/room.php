<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class room extends Model
{
     protected $guarded = [];
    /**
     * Get the user that owns the apartment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apartment()
    {
        return $this->belongsTo(apartment::class);
    }
    /**
     * Scope a query to only include users with the given apartment_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $apartment_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByApartmentId($query, $apartment_id)
    {
        return $query->where('apartment_id', '=', $apartment_id);
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
