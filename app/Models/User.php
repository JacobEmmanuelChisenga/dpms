<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_FLEET_OFFICER = 'fleet_officer';

    public const ROLE_MANAGEMENT = 'management';

    /** @deprecated Unused login role; retained so legacy portal accounts receive a helpful sign-in refusal. */
    public const ROLE_DRIVER = 'driver';

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
        ];
    }

    public function issuedPermits(): HasMany
    {
        return $this->hasMany(Permit::class, 'issued_by');
    }

    public function driverProfile(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function isDriver(): bool
    {
        return $this->role === self::ROLE_DRIVER;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isFleetOfficer(): bool
    {
        return $this->role === self::ROLE_FLEET_OFFICER;
    }

    public function isManagement(): bool
    {
        return $this->role === self::ROLE_MANAGEMENT;
    }

    /** Fleet officer operational UI. */
    public function usesFleetLayout(): bool
    {
        return $this->isFleetOfficer();
    }

    public function usesManagementLayout(): bool
    {
        return $this->isManagement();
    }

    /**
     * @return array<string, string>
     */
    public static function assignableRoles(): array
    {
        return [
            self::ROLE_ADMIN => __('Administrator'),
            self::ROLE_FLEET_OFFICER => __('Fleet Management Officer'),
            self::ROLE_MANAGEMENT => __('Management'),
        ];
    }
}
