<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class MediaUploadService
{
    public function storeImage(UploadedFile $file, User $user, ?string $altText = null): Media
    {
        $path = $file->store('media/'.now()->format('Y/m'), 'public');
        [$width, $height] = $this->imageDimensions($file);

        return Media::create([
            'user_id' => $user->id,
            'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize() ?: 0,
            'disk' => 'public',
            'path' => $path,
            'media_type' => 'image',
            'width' => $width,
            'height' => $height,
            'alt_text' => $altText,
        ]);
    }

    public function publicUrl(Media $media): string
    {
        return '/storage/'.$media->path;
    }

    private function imageDimensions(UploadedFile $file): array
    {
        $dimensions = @getimagesize($file->getRealPath());

        return [
            $dimensions[0] ?? null,
            $dimensions[1] ?? null,
        ];
    }
}
