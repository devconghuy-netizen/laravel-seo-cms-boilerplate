<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Post;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private AuditLogService $auditLogService)
    {
    }

    public function show(Request $request)
    {
        $user = $request->user()->load('roles');

        $stats = [
            'posts' => Post::where('author_id', $user->id)->count(),
            'published_posts' => Post::where('author_id', $user->id)->where('status', 'published')->count(),
            'draft_posts' => Post::where('author_id', $user->id)->where('status', 'draft')->count(),
            'audit_logs' => AuditLog::where('user_id', $user->id)->count(),
        ];

        $recentPosts = Post::with('category')
            ->where('author_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $activityLogs = AuditLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('profile.show', compact('user', 'stats', 'recentPosts', 'activityLogs'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ]);

        $oldValues = $user->only(['name', 'phone_number']);
        $user->update($data);

        $this->auditLogService->log(
            $request,
            $user,
            'profile.updated',
            $oldValues,
            $user->fresh()->only(['name', 'phone_number']),
            'Updated profile information.'
        );

        return redirect()
            ->route('profile.show')
            ->with('status', 'Đã cập nhật hồ sơ.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        $this->auditLogService->log(
            $request,
            $user,
            'profile.password_updated',
            [],
            ['password_updated' => true],
            'Updated account password.'
        );

        return redirect()
            ->route('profile.show')
            ->with('status', 'Đã cập nhật mật khẩu.');
    }
}
