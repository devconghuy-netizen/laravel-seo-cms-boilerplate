<?php

namespace App\Services;

use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService
{
    public function __construct(private PostRepository $repository)
    {
    }

    public function listPublished(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->getPublishedPaginated($perPage);
    }

    public function searchPublished(?string $term, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->searchPublishedPaginated($term, $perPage);
    }

    public function findBySlug(string $slug)
    {
        return $this->repository->findBySlugWithRelations($slug);
    }
}
