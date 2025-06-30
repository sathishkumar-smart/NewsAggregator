<?php


namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
    public function getPaginated(array $filters): LengthAwarePaginator;
    public function findById(int $id): ?array;
}