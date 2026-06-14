<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class AdminMediaController extends Controller
{
    public function index()
    {
        $this->authorize('manageAll', Post::class);

        $mediaItems = Media::with('user')
            ->images()
            ->latest()
            ->paginate(24);

        return view('admin.media.index', compact('mediaItems'));
    }

    public function destroy(Media $media)
    {
        $this->authorize('manageAll', Post::class);

        $disk = Storage::disk($media->disk);

        if ($disk->exists($media->path)) {
            $disk->delete($media->path);
        }

        if ($disk->exists($media->path) && method_exists($disk, 'path')) {
            @unlink($disk->path($media->path));
        }

        $media->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Đã xóa media.');
    }
}
