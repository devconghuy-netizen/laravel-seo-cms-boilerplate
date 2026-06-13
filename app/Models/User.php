<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone_number', 'is_active'])]
#[Hidden(['password', 'remember_token', 'two_fa_secret', 'two_fa_backup_codes'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_fa_enabled' => 'boolean',
            'is_active' => 'boolean',
            'two_fa_backup_codes' => 'encrypted:json',
        ];
    }

    /**
     * Get all roles for the user.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            Role::class,
            'model',
            'model_has_roles',
            'model_id',
            'role_id'
        )
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    /**
     * Get all permissions for the user through their roles.
     */
    public function permissions()
    {
        return Permission::query()
            ->join('role_has_permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->join('model_has_roles', 'role_has_permissions.role_id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', self::class)
            ->where('model_has_roles.model_id', $this->id)
            ->distinct();
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(Role|string ...$roles): self
    {
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::query()->whereName($role)->firstOrFail();
            }

            $this->roles()->syncWithoutDetaching([$role->id]);
        }

        return $this;
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(Role|string ...$roles): self
    {
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::query()->whereName($role)->firstOrFail();
            }

            $this->roles()->detach($role->id);
        }

        return $this;
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(Role|string ...$roles): bool
    {
        foreach ($roles as $role) {
            if (is_string($role)) {
                $role = Role::query()->whereName($role)->first();
            }

            if ($this->roles()->where('role_id', $role->id ?? null)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasPermission(Permission|string ...$permissions): bool
    {
        $userPermissions = $this->permissions()->pluck('permissions.id')->toArray();

        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Permission::query()->whereName($permission)->first();
            }

            if ($permission && in_array($permission->id, $userPermissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
