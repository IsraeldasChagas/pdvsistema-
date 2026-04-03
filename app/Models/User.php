<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\PublicStorage;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        'telefone',
        'parceiro_tipo_documento',
        'parceiro_documento',
        'parceiro_razao_social',
        'endereco_logradouro',
        'endereco_numero',
        'endereco_complemento',
        'endereco_bairro',
        'endereco_cidade',
        'endereco_uf',
        'endereco_cep',
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

        $path = $this->avatar_path;

        // Legado: storage/app/public/users/avatars/... (exige public/storage)
        if (str_starts_with($path, 'users/avatars/')) {
            return PublicStorage::url($path);
        }

        // Atual: public/pdv/user-avatars/...
        $path = ltrim($path, '/');
        if (! app()->runningInConsole() && request()->hasHeader('Host')) {
            $base = rtrim(request()->getSchemeAndHttpHost().request()->getBasePath(), '/');

            return $base.'/pdv/'.$path;
        }

        return asset('pdv/'.$path);
    }

    /**
     * Remove arquivo de avatar (formato novo ou legado).
     */
    public static function deleteStoredAvatarFile(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }
        if (str_starts_with($path, 'users/avatars/')) {
            Storage::disk('public')->delete($path);

            return;
        }
        Storage::disk('pdv_public')->delete($path);
    }

    /**
     * Grava upload em public/pdv/user-avatars/ e retorna o caminho relativo ao disco.
     */
    public static function storeAvatarFile(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?: 'jpg';
        $name = Str::uuid()->toString().'.'.$ext;
        $path = $file->storeAs('user-avatars', $name, 'pdv_public');
        if ($path === false || $path === '') {
            throw new \RuntimeException('Falha ao gravar foto em public/pdv (permissões ou disco cheio?).');
        }

        return $path;
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
        if ($role === 'super_admin') {
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

        // Compatibilidade com dados legados: null = acesso total (exceto bloqueios por empresa).
        if ($this->allowed_screens === null) {
            return true;
        }

        $screens = $this->allowed_screens;

        return in_array($key, $screens, true);
    }

    public function defaultScreensCheckedForForm(): array
    {
        // Defaults para novos usuários (vendedor/gerente). Admin e super-admin ignoram isso (têm acesso total).
        return ['dashboard', 'produtos', 'mini_pdv', 'financeiro'];
    }

    public function screensCheckedForForm(): array
    {
        if ($this->isSuperAdmin()) {
            return collect(config('pdv.screens', []))->pluck('key')->all();
        }

        if ($this->allowed_screens === null) {
            return collect(config('pdv.screens', []))->pluck('key')->all();
        }

        return $this->allowed_screens;
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
