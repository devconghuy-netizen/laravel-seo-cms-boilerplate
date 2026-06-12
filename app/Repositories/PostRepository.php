<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function getPublishedPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return Post::published()->with(['category', 'author'])->paginate($perPage);
    }

    public function findBySlugWithRelations(string $slug): ?Post
    {
        return Post::where('slug', $slug)->with(['category', 'author', 'tags', 'seoMeta'])->first();
    }
}
