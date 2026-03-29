<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\PublicStorage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'vendedor_rua',
        'allowed_screens',
        'is_active',
        'avatar_path',
    ];

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
            'vendedor_rua' => 'boolean',
            'allowed_screens' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdministrador(): bool
    {
        return $this->role === 'administrador';
    }

    public function isGerente(): bool
    {
        return $this->role === 'gerente';
    }

    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin() || $this->isAdministrador() || $this->isGerente();
    }

    /**
     * URL pública da foto (ou null).
     */
    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return PublicStorage::url($this->avatar_path);
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @param  list<string>|null  $screens
     */
    public function syncAllowedScreensFromInput(?array $screens, string $role): void
    {
        if ($role === 'administrador' || $role === 'super_admin') {
            $this->allowed_screens = null;

            return;
        }

        $valid = collect(config('pdv.screens', []))->pluck('key')->all();
        $this->allowed_screens = array_values(array_intersect($valid, $screens ?? []));
    }

    public function hasScreenAccess(string $key): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->is_active) {
            return false;
        }

        $company = $this->company;
        if ($company !== null && ! $company->allowsTenantScreen($key)) {
            return false;
        }

        if ($this->isAdministrador()) {
            return true;
        }

        $screens = $this->allowed_screens ?? [];

        return in_array($key, $screens, true);
    }

    public function defaultScreensCheckedForForm(): array
    {
        return ['dashboard', 'produtos', 'mini_pdv'];
    }

    public function screensCheckedForForm(): array
    {
        if ($this->isSuperAdmin() || $this->isAdministrador()) {
            return collect(config('pdv.screens', []))->pluck('key')->all();
        }

        return $this->allowed_screens ?? $this->defaultScreensCheckedForForm();
    }

    /**
     * @return HasMany<VendorStock, $this>
     */
    public function vendorStocks(): HasMany
    {
        return $this->hasMany(VendorStock::class);
    }

    /**
     * @return HasMany<CashRegisterSession, $this>
     */
    public function cashRegisterSessions(): HasMany
    {
        return $this->hasMany(CashRegisterSession::class);
    }
}
