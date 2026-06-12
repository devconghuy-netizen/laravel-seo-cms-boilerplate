<?php

namespace App\DTOs;

readonly class TagDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $color = null,
        public ?int $sortOrder = 0,
        public ?bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            color: $data['color'] ?? null,
            sortOrder: $data['sort_order'] ?? 0,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'color' => $this->color,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}
