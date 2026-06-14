<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function getPublishedPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Post::published()->with(['category', 'author', 'tags'])->paginate($perPage);
    }

    public function searchPublishedPaginated(?string $term, int $perPage = 10): LengthAwarePaginator
    {
        $term = trim((string) $term);

        return Post::published()
            ->with(['category', 'author', 'tags'])
            ->when($term !== '', function ($query) use ($term) {
                $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

                $query->where(function ($query) use ($like) {
                    $query->where('slug', 'like', $like)
                        ->orWhereHas('translations', function ($query) use ($like) {
                            $query->whereIn('key', ['title', 'excerpt', 'content'])
                                ->where('value', 'like', $like);
                        });
                });
            })
            ->latest('published_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findBySlugWithRelations(string $slug): ?Post
    {
        return Post::published()
            ->where('slug', $slug)
            ->with(['category', 'author', 'tags', 'seoMeta'])
            ->first();
    }
}
