<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'is_premium', 'premium_until'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

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
            'is_premium' => 'boolean',
            'premium_until' => 'datetime',
        ];
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'author_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function editedMagazineIssues(): HasMany
    {
        return $this->hasMany(MagazineIssue::class, 'editor_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(['super_admin', 'dergi_editoru']);
    }

    /**
     * Girişten sonra role göre yönlendirilecek yol.
     * Süper Admin/Dergi Editörü -> Filament admin paneli, Yazar -> Yayınlarım, Okur -> Kişisel Kütüphanem.
     */
    public function redirectPath(): string
    {
        if ($this->hasAnyRole(['super_admin', 'dergi_editoru'])) {
            return '/admin';
        }

        if ($this->hasRole('yazar')) {
            return '/panel/yayinlarim';
        }

        return '/panel';
    }
}
