<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->hasPermission('manage-users'), 403);

        $filters = [
            'action' => $request->query('action'),
            'user_id' => $request->query('user_id'),
        ];

        $logs = AuditLog::with('user')
            ->when($filters['action'], fn ($query, string $action) => $query->where('action', $action))
            ->when($filters['user_id'], fn ($query, string $userId) => $query->where('user_id', $userId))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = User::whereIn('id', AuditLog::query()->select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('name')
            ->get();

        return view('admin.audit-logs.index', compact('logs', 'actions', 'users', 'filters'));
    }
}
