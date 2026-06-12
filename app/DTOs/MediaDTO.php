<?php

namespace App\DTOs;

readonly class MediaDTO
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $originalFilename,
        public string $mimeType,
        public int $size,
        public string $path,
        public string $mediaType,
        public ?int $width = null,
        public ?int $height = null,
        public ?string $altText = null,
        public ?string $description = null,
        public ?array $metadata = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? 0,
            name: $data['name'] ?? '',
            originalFilename: $data['original_filename'] ?? '',
            mimeType: $data['mime_type'] ?? 'application/octet-stream',
            size: $data['size'] ?? 0,
            path: $data['path'] ?? '',
            mediaType: $data['media_type'] ?? 'document',
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
            altText: $data['alt_text'] ?? null,
            description: $data['description'] ?? null,
            metadata: $data['metadata'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'name' => $this->name,
            'original_filename' => $this->originalFilename,
            'mime_type' => $this->mimeType,
            'size' => $this->size,
            'path' => $this->path,
            'media_type' => $this->mediaType,
            'width' => $this->width,
            'height' => $this->height,
            'alt_text' => $this->altText,
            'description' => $this->description,
            'metadata' => $this->metadata,
        ];
    }
}
