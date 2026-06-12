<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'module', 'resource', 'action', 'is_system', 'sort_order'];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Get all roles with this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_has_permissions')
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    /**
     * Scope: Get permissions by module.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope: Get permissions by resource.
     */
    public function scopeByResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope: Get permissions by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get all system permissions.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope: Get all custom permissions.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Get permission key for gate checks (resource:action).
     */
    public function getKeyAttribute(): string
    {
        return $this->resource . ':' . $this->action;
    }
}
