<?php

namespace App\DTOs;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $description = null,
        public ?int $parentId = null,
        public ?int $sortOrder = 0,
        public ?bool $isActive = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            sortOrder: $data['sort_order'] ?? 0,
            isActive: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'sort_order' => $this->sortOrder,
            'is_active' => $this->isActive,
        ];
    }
}
