<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use App\Models\Source;
use App\Models\Author;
use Carbon\Carbon;

class NewYorkTimesApiService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.newyorktimes.key');
        $this->baseUrl = config('services.newyorktimes.base_url');
    }

    /**
     * Fetch articles from NYT API.
     *
     * @return array
     */
    public function fetchArticles(): array
    {
        $response = Http::get($this->baseUrl, [
            'api-key' => $this->apiKey,
            'sort' => 'newest',
            'fq' => 'document_type:("article")',
        ]);

        if (!$response->successful()) {
            logger()->error('NYT API fetch failed', ['response' => $response->body()]);
            return [];
        }

        return $response->json('response.docs') ?? [];
    }

    /**
     * Store a single NYT article into the database.
     *
     * @param array $data
     * @return void
     */
    public function storeArticle(array $data): void
    {
        // Source - always "The New York Times"
        $source = Source::firstOrCreate([
            'id' => 'nyt',
            'name' => 'The New York Times',
        ]);

        // Author
        $authorName = $data['byline']['original'] ?? 'NYT Staff';
        $author = Author::firstOrCreate([
            'name' => $authorName,
        ]);

        // Image (use articleLarge or fallback)
        $imageUrl = $data['multimedia']['default']['url'] ?? null;

        Article::updateOrCreate(
            ['url' => $data['web_url']],
            [
                'title' => $data['headline']['main'] ?? 'Untitled',
                'description' => $data['abstract'] ?? $data['snippet'] ?? null,
                'url_to_image' => $imageUrl,
                'published_at' => Carbon::parse($data['pub_date']),
                'content' => $data['abstract'] ?? $data['snippet'] ?? null,
                'source_id' => $source->id,
                'author_id' => $author->id,
            ]
        );
    }
}
