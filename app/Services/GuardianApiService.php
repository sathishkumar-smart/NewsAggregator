<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Source;
use App\Models\Author;
use Carbon\Carbon;

class GuardianApiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = config('services.guardian.base_url');
    }

    /**
     * Fetch articles from The Guardian API.
     *
     * @return array
     */
    public function fetchArticles(): array
    {
        $response = Http::get("{$this->baseUrl}/search", [
            'api-key' => $this->apiKey,
            'order-by' => 'newest',
            'page-size' => 10,
            'show-fields' => 'trailText,headline,thumbnail,body',
        ]);

        if (!$response->successful()) {
            logger()->error('Guardian API fetch failed', ['response' => $response->body()]);
            return [];
        }

        return $response->json('response.results') ?? [];
    }

    /**
     * Store a single Guardian article into the database.
     *
     * @param array $data
     * @return void
     */
    public function storeArticle(array $data): void
    {
        // Source - always "The Guardian"
        $source = Source::firstOrCreate([
            'id' => 'guardian',
            'name' => 'The Guardian',
        ]);

        // Author is not provided, default to null or "Guardian Staff"
        $author = Author::firstOrCreate([
            'name' => 'Guardian Staff'
        ]);

        Article::updateOrCreate(
            ['url' => $data['webUrl']],
            [
                'title' => $data['webTitle'],
                'description' => $data['fields']['trailText'] ?? null,
                'url_to_image' => $data['fields']['thumbnail'] ?? null,
                'published_at' => Carbon::parse($data['webPublicationDate']),
                'content' => $data['fields']['body'] ?? null,
                'source_id' => $source->id,
                'author_id' => $author->id,
            ]
        );
    }
}
