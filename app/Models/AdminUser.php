<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminUser extends Model implements JWTSubject, Authenticatable
{
    use HasUuids;

    protected $table = 'admin_users';
    public $timestamps = false;
    
    protected $fillable = [
        'username',
        'password',
        'role',
    ];
    
    protected $hidden = [
        'password',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'username' => $this->username,
            'role' => $this->role,
        ];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }
}
