<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminHealthController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()?->hasPermission('manage-users'), 403);

        $checks = [
            $this->appCheck(),
            $this->databaseCheck(),
            $this->storageCheck(),
            $this->cacheCheck(),
            $this->queueCheck(),
            $this->sessionCheck(),
            $this->logsCheck(),
        ];

        $overallOk = collect($checks)->every(fn (array $check) => $check['status'] === 'ok');

        return view('admin.health.index', compact('checks', 'overallOk'));
    }

    private function appCheck(): array
    {
        return $this->ok('Application', sprintf(
            '%s environment, debug %s',
            app()->environment(),
            config('app.debug') ? 'enabled' : 'disabled'
        ));
    }

    private function databaseCheck(): array
    {
        try {
            DB::select('select 1 as health_check');

            return $this->ok('Database', sprintf('Connected via %s', config('database.default')));
        } catch (Throwable $exception) {
            return $this->fail('Database', $exception->getMessage());
        }
    }

    private function storageCheck(): array
    {
        $publicStoragePath = public_path('storage');
        $appPublicPath = storage_path('app/public');
        $publicPathExists = file_exists($publicStoragePath);
        $appPublicExists = is_dir($appPublicPath);

        if ($publicPathExists && $appPublicExists) {
            return $this->ok('Storage', 'Public storage path is available');
        }

        $missing = collect([
            $publicPathExists ? null : 'public/storage',
            $appPublicExists ? null : 'storage/app/public',
        ])->filter()->implode(', ');

        return $this->fail('Storage', 'Missing: '.$missing);
    }

    private function cacheCheck(): array
    {
        return $this->ok('Cache', 'Driver: '.(config('cache.default') ?? 'unknown'));
    }

    private function queueCheck(): array
    {
        return $this->ok('Queue', 'Driver: '.(config('queue.default') ?? 'unknown'));
    }

    private function sessionCheck(): array
    {
        return $this->ok('Session', 'Driver: '.(config('session.driver') ?? 'unknown'));
    }

    private function logsCheck(): array
    {
        if (is_writable(storage_path('logs'))) {
            return $this->ok('Logs', 'storage/logs is writable');
        }

        return $this->fail('Logs', 'storage/logs is not writable');
    }

    private function ok(string $name, string $details): array
    {
        return [
            'name' => $name,
            'status' => 'ok',
            'details' => $details,
        ];
    }

    private function fail(string $name, string $details): array
    {
        return [
            'name' => $name,
            'status' => 'fail',
            'details' => $details,
        ];
    }
}
