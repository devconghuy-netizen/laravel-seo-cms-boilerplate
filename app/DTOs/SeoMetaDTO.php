<?php

namespace App\DTOs;

readonly class SeoMetaDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $keywords = null,
        public ?string $canonicalUrl = null,
        public ?string $ogTitle = null,
        public ?string $ogDescription = null,
        public ?string $ogImage = null,
        public ?string $ogType = 'website',
        public ?string $twitterCard = 'summary',
        public ?string $twitterHandle = null,
        public ?array $structuredData = null,
        public ?bool $index = true,
        public ?bool $follow = true,
        public ?string $locale = 'en',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            description: $data['description'] ?? '',
            keywords: $data['keywords'] ?? null,
            canonicalUrl: $data['canonical_url'] ?? null,
            ogTitle: $data['og_title'] ?? null,
            ogDescription: $data['og_description'] ?? null,
            ogImage: $data['og_image'] ?? null,
            ogType: $data['og_type'] ?? 'website',
            twitterCard: $data['twitter_card'] ?? 'summary',
            twitterHandle: $data['twitter_handle'] ?? null,
            structuredData: $data['structured_data'] ?? null,
            index: $data['index'] ?? true,
            follow: $data['follow'] ?? true,
            locale: $data['locale'] ?? 'en',
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'canonical_url' => $this->canonicalUrl,
            'og_title' => $this->ogTitle,
            'og_description' => $this->ogDescription,
            'og_image' => $this->ogImage,
            'og_type' => $this->ogType,
            'twitter_card' => $this->twitterCard,
            'twitter_handle' => $this->twitterHandle,
            'structured_data' => $this->structuredData,
            'index' => $this->index,
            'follow' => $this->follow,
            'locale' => $this->locale,
        ];
    }
}
