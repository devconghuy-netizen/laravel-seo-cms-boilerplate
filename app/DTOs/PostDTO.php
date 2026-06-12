<?php

namespace App\DTOs;

readonly class PostDTO
{
    public function __construct(
        public string $title,
        public string $slug,
        public string $content,
        public int $categoryId,
        public int $authorId,
        public ?string $excerpt = null,
        public ?string $featuredImage = null,
        public ?string $status = 'draft',
        public ?array $tagIds = [],
        public ?bool $isFeatured = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? '',
            content: $data['content'] ?? '',
            categoryId: $data['category_id'] ?? 0,
            authorId: $data['author_id'] ?? 0,
            excerpt: $data['excerpt'] ?? null,
            featuredImage: $data['featured_image'] ?? null,
            status: $data['status'] ?? 'draft',
            tagIds: $data['tag_ids'] ?? [],
            isFeatured: $data['is_featured'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'slug' => $this->slug,
            'category_id' => $this->categoryId,
            'author_id' => $this->authorId,
            'featured_image' => $this->featuredImage,
            'status' => $this->status,
            'is_featured' => $this->isFeatured,
        ];
    }
}
