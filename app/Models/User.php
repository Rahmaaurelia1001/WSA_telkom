<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Validasi sebelum data disimpan
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            $validator = Validator::make($user->toArray(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . ($user->id ?? 'null'),
                'password' => 'nullable|min:8', // Hanya jika password diubah
                'role' => 'required|in:admin,user,editor', // Sesuaikan dengan role yang diizinkan
            ]);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }
        });
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Fungsi untuk mengecek role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
