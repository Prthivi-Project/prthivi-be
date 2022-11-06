<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        "phone",
        "avatar",
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    // relational
    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id');
        // return $this->belongsToMany(Role::class, 'user_roles', "user_id", "role_id");
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, Store::class);
    }


    // helper
    public function isSuperAdministrator()
    {
        return $this->role_id === 1; // super admin id = 1
    }

    public function isAdministrator()
    {
        return $this->role_id === 2; // admin id adalah 2
    }

    public function isVendor()
    {
        return $this->role_id === 3; // vendor id adalah 3
    }

    public function isCustomer()
    {
        return $this->role_id === 4; // vendor id adalah 3
    }



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role_id
        ];
    }
}
