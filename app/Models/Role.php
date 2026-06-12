<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_system', 'sort_order'];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get all permissions associated with this role.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    /**
     * Get all users with this role.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        )
            ->withTimestamps();
    }

    /**
     * Assign a permission to this role.
     */
    public function givePermissionTo(Permission|string ...$permissions): self
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Permission::query()->whereName($permission)->firstOrFail();
            }

            $this->permissions()->attach($permission->id);
        }

        return $this;
    }

    /**
     * Revoke a permission from this role.
     */
    public function revokePermissionFrom(Permission|string ...$permissions): self
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Permission::query()->whereName($permission)->firstOrFail();
            }

            $this->permissions()->detach($permission->id);
        }

        return $this;
    }

    /**
     * Check if role has a specific permission.
     */
    public function hasPermission(Permission|string ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if (is_string($permission)) {
                $permission = Permission::query()->whereName($permission)->first();
            }

            if ($this->permissions()->where('permission_id', $permission->id ?? null)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Scope: Get all system roles.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope: Get all custom roles.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }
}
