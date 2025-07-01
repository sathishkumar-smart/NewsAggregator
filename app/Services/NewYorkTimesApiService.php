<?php

namespace App\Services;

use App\Models\Category;
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
     * Fetch and store articles from NYT Top Stories API by category.
     *
     * @return void
     */
    public function fetchArticles(): void
    {
        try {
            $categories = Category::all(); // Seeded categories

            foreach ($categories as $category) {
                $response = Http::get("{$this->baseUrl}", [
                    'api-key' => $this->apiKey,
                    'q' => $category->name
                ]);

                if (!$response->successful()) {
                    storeCustomLogs(
                        ['when' => "NYT API fetch failed for category: {$category}", "response" => $response->body()],
                        'services/newyorktimes-api'
                    );
                    continue;
                }

                $articles = $response->json('results') ?? [];

                foreach ($articles as $article) {
                    $this->storeArticle($article, $category);
                }
            }
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/newyorktimes-api');
        }
    }

    /**
     * Store a single NYT article into the database.
     *
     * @param array $data
     * @param string $categoryName
     * @return void
     */
    public function storeArticle(array $data, string $categoryName): void
    {
        try {
            // Source - always "The New York Times"
            $source = Source::firstOrCreate([
                'name' => 'The New York Times',
            ]);

            // Author (fallback to 'NYT Staff' if not found)
            $authorName = $data['byline'] ?? 'NYT Staff';
            $author = Author::firstOrCreate([
                'name' => $authorName,
            ]);

            // Category
            $category = Category::firstOrCreate([
                'name' => ucfirst($categoryName),
            ]);

            // Image (get the first large image if exists)
            $imageUrl = null;
            if (!empty($data['multimedia'])) {
                foreach ($data['multimedia'] as $media) {
                    if (($media['format'] ?? '') === 'superJumbo') {
                        $imageUrl = $media['url'];
                        break;
                    }
                }
            }

            Article::updateOrCreate(
                ['url' => $data['url']],
                [
                    'title' => $data['title'] ?? 'Untitled',
                    'description' => $data['abstract'] ?? null,
                    'url_to_image' => $imageUrl,
                    'published_at' => Carbon::parse($data['published_date']),
                    'content' => $data['abstract'] ?? null,
                    'source_id' => $source->id,
                    'author_id' => $author->id,
                    'category_id' => $category->id,
                ]
            );
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/newyorktimes-api');
        }
    }
}
