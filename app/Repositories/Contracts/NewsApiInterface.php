<?php

namespace App\Repositories\Contracts;

interface NewsApiInterface
{
    /**
     * Fetch articles from the API.
     *
     * @return array
     */
    public function fetchArticles(): array;

    /**
     * Store a single article into the database.
     *
     * @param array $data
     * @return void
     */
    public function storeArticle(array $data): void;
}