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
            ->with(['category', 'author', 'tags', 'seoMeta', 'affiliateLinks'])
            ->first();
    }

    public function getRelatedPublished(Post $post, int $limit = 3)
    {
        $tagIds = $post->tags->pluck('id');

        return Post::published()
            ->whereKeyNot($post->id)
            ->where(function ($query) use ($post, $tagIds) {
                $query->where('category_id', $post->category_id);

                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', fn ($query) => $query->whereIn('tags.id', $tagIds));
                }
            })
            ->with(['category', 'author', 'tags'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
