<?php

namespace App\Services;

use App\Repositories\Contracts\ArticleRepositoryInterface;

class ArticleService
{
    public function __construct(protected ArticleRepositoryInterface $articleRepository)
    {
    }

    /**
     * Handle fetching paginated & filtered article list
     */
    public function listArticles(array $filters): mixed
    {
        return $this->articleRepository->getPaginated($filters);
    }

    /**
     * Get single article details
     */
    public function getArticleById(int $id): mixed
    {
        return $this->articleRepository->findById($id);
    }
}
