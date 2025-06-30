<?php

namespace App\Services;

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
     * Fetch articles from The Guardian API.
     *
     * @return array
     */
    public function fetchArticles(): array
    {
        try {

            storeCustomLogs(['service' => 'News API', 'api_key' => $this->apiKey], 'services/newsapi');
            $response = Http::get("{$this->baseUrl}/everything", [
                'apiKey' => $this->apiKey,
                'q' => 'software',
                'order-by' => 'newest',
                'page-size' => 10,
                'show-fields' => 'trailText,headline,thumbnail,body',
            ]);

            if (!$response->successful()) {
                storeCustomLogs(['service' => 'News API fetch failed', 'response' => $response->body()], 'services/newsapi');
                return [];
            }

            return $response->json('response.results') ?? [];
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/newsapi');
            return [];
        }
    }

    protected function storeArticle(array $data): void
    {
        $source = Source::firstOrCreate([
            'id' => $data['source']['id'] ?? null,
            'name' => $data['source']['name'],
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
                'url_to_image' => $data['urlToImage'],
                'published_at' => Carbon::parse($data['publishedAt']),
                'content' => $data['content'],
                'source_id' => $source->id,
                'author_id' => $author?->id,
            ]
        );
    }
}
