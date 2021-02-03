<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\User;

class apartment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'image',
        'hub',
        'address',
        'status'
    ];

    /**
     * Get the user that owns the apartment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
