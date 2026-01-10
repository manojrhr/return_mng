<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'store_name',
        'store_code',
        'point_of_contact',
        'email',
        'phone',
        'address'
    ];

    /**
     * Get the users for the store.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
