<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Source;
use App\Models\Author;
use Carbon\Carbon;

class NewsApiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = config('services.newsapi.base_url');
    }

    /**
     * Fetch and store articles by category.
     */
    public function fetchArticles(): void
    {
        try {
            $categories = Category::all();

            foreach ($categories as $category) {
                $response = Http::get("{$this->baseUrl}/everything", [
                    'apiKey' => $this->apiKey,
                    'q' => $category->name,
                    'sortBy' => 'publishedAt',
                    'language' => 'en',
                    'pageSize' => 10,
                ]);

                if (!$response->successful()) {
                    storeCustomLogs(['service' => 'News API fetch failed', 'response' => $response->body()], 'services/newsapi');
                    continue;
                }

                $articles = $response->json('articles');

                foreach ($articles as $article) {
                    $this->storeArticle($article, $category->id);
                }
                sleep(2);
            }
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/newsapi');
        }
    }

    protected function storeArticle(array $data, int $categoryId): void
    {
        $source = Source::firstOrCreate([
            'name' => $data['source']['name'] ?? 'Unknown Source',
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
                'content' => $data['content'],
                'url' => $data['url'],
                'image_url' => $data['urlToImage'] ?? null,
                'published_at' => Carbon::parse($data['publishedAt']),
                'source_id' => $source->id,
                'author_id' => $author?->id,
                'category_id' => $categoryId,
            ]
        );
    }
}
