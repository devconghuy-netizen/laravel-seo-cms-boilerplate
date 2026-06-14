<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(private AuditLogService $auditLogService)
    {
    }

    public function index(Request $request)
    {
        $this->authorizeManageUsers($request);

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'role' => $request->query('role'),
            'status' => $request->query('status'),
        ];

        $users = User::with('roles')
            ->when($filters['q'], function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($filters['role'], fn ($query, string $role) => $query->whereHas('roles', fn ($query) => $query->where('name', $role)))
            ->when($filters['status'] !== null && $filters['status'] !== '', fn ($query) => $query->where('is_active', $filters['status'] === 'active'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('sort_order')->orderBy('name')->get();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins' => User::whereHas('roles', fn ($query) => $query->where('name', 'admin'))->count(),
        ];

        return view('admin.users.index', compact('users', 'roles', 'filters', 'stats'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeManageUsers($request);

        $data = $request->validate([
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $request->boolean('is_active');

        if ($request->user()->is($user) && ! $isActive) {
            return back()->withErrors(['is_active' => 'Bạn không thể tự tắt tài khoản của mình.']);
        }

        $oldValues = [
            'role_ids' => $user->roles()->pluck('roles.id')->values()->all(),
            'is_active' => $user->is_active,
        ];

        $user->roles()->sync($data['role_ids'] ?? []);
        $user->update(['is_active' => $isActive]);

        $this->auditLogService->log(
            $request,
            $user,
            'user.updated',
            $oldValues,
            [
                'role_ids' => $user->roles()->pluck('roles.id')->values()->all(),
                'is_active' => $user->fresh()->is_active,
            ],
            'Updated user roles or account status.'
        );

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Đã cập nhật user.');
    }

    private function authorizeManageUsers(Request $request): void
    {
        abort_unless($request->user()?->hasPermission('manage-users'), 403);
    }
}
