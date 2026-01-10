<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'store_id'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Ensure role_id is set if not provided or is null
            if (empty($user->role_id) || is_null($user->role_id)) {
                try {
                    $defaultRole = Role::where('name', 'client_user')->first() 
                        ?? Role::where('name', 'store_user')->first()
                        ?? Role::first();
                    
                    if ($defaultRole) {
                        $user->role_id = $defaultRole->id;
                    } else {
                        // If no roles exist, create a default one
                        $defaultRole = Role::create(['name' => 'client_user']);
                        $user->role_id = $defaultRole->id;
                    }
                } catch (\Exception $e) {
                    // If roles table doesn't exist yet, we'll handle it in migration
                    // For now, set a temporary value that will be updated later
                    $user->role_id = 1;
                }
            }
        });
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
