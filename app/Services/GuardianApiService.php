<?php

namespace App\Services;

use App\Models\Category;
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
        try {

            $response = Http::get("{$this->baseUrl}/search", [
                'api-key' => $this->apiKey,
                'order-by' => 'newest',
                'show-tags' => 'contributor',
                'page-size' => 10,
                'show-fields' => 'trailText,headline,thumbnail,body',
            ]);

            if (!$response->successful()) {
                logger()->error('Guardian API fetch failed', ['response' => $response->body()]);
                return [];
            }

            $articles = $response->json('response.results') ?? [];

            foreach ($articles as $article) {
                $this->storeArticle($article);
            }

            return $articles;
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/guardianapi');
            return [];
        }
    }

    /**
     * Store a single Guardian article into the database.
     *
     * @param array $data
     * @return void
     */
    public function storeArticle(array $data): void
    {
        try {
            // Source - always "The Guardian"
            $source = Source::firstOrCreate([
                'name' => 'The Guardian',
            ]);

            // Extract first contributor from tags
            $authorName = 'Guardian Staff'; // default fallback
            if (!empty($data['tags'])) {
                foreach ($data['tags'] as $tag) {
                    if ($tag['type'] === 'contributor') {
                        $authorName = $tag['webTitle'];
                        break;
                    }
                }
            }

            $author = Author::firstOrCreate([
                'name' => $authorName,
            ]);

            // Get or create category from pillarName
            $category = null;
            if (!empty($data['pillarName'])) {
                $category = Category::firstOrCreate([
                    'name' => $data['pillarName'],
                ]);
            }

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
                    'category_id' => $category?->id,
                ]
            );
        } catch (\Throwable $th) {
            storeCustomLogsThrowable($th, 'services/guardianapi');
        }
    }
}
