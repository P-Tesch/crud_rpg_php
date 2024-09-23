<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** @var array<int, string> */
    protected $fillable = [
        'login'
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
        'sheet_id',
        'is_admin'
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** @return HasOne<Sheet> */
    public function sheet() : HasOne {
        return $this->hasOne(Sheet::class);
    }

    public function canAccessPanel(Panel $panel) : bool {
        return $this->is_admin;
    }

    public function getFilamentName() : string {
        return "Admin";
    }
}
