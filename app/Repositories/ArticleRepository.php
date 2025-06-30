<?php

namespace App\Repositories;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
    /**
     * Get paginated and optionally filtered articles
     */
    public function getPaginated(array $filters): LengthAwarePaginator
    {
        return Article::with(['author', 'source', 'category'])
            ->when(
                !empty($filters['keyword']),
                fn($q) =>
                $q->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['keyword'] . '%')
            )
            ->when(
                !empty($filters['source_id']),
                fn($q) =>
                $q->where('source_id', $filters['source_id'])
            )
            ->when(
                !empty($filters['category_id']),
                fn($q) =>
                $q->where('category_id', $filters['category_id'])
            )
            ->when(
                !empty($filters['date']),
                fn($q) =>
                $q->whereDate('published_at', $filters['date'])
            )
            ->latest()
            ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Find article by ID with relationships
     */
    public function findById(int $id): ?array
    {
        $article = Article::with(['author', 'source', 'category'])->find($id);
        return $article ? $article->toArray() : null;
    }

    /**
     * Deals storing and updating of the articles.
     * @param array $data
     * @return void
     */
    public function storeOrUpdate(array $data): void
    {
        $source = Source::firstOrCreate([
            'source_id' => $data['source']['id'] ?? null,
            'name' => $data['source']['name'] ?? 'Unknown'
        ]);

        $author = null;
        if (!empty($data['author'])) {
            $author = Author::firstOrCreate(['name' => $data['author']]);
        }

        Article::updateOrCreate(
            ['url' => $data['url']],
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'url_to_image' => $data['urlToImage'] ?? null,
                'published_at' => Carbon::parse($data['publishedAt']),
                'content' => $data['content'] ?? '',
                'source_id' => $source->id,
                'author_id' => $author?->id,
            ]
        );
    }
}
