<?php

namespace App\Services;

use App\Services\Contracts\NewsApiInterface;
use App\Repositories\Contracts\ArticleRepositoryInterface;

/**
 * Class AggregatorService
 *
 * Aggregates articles from multiple news APIs.
 */
class AggregatorService
{
    /**
     * List of news API services implementing NewsApiInterface.
     *
     * @var NewsApiInterface[]
     */
    protected array $apis;

    /**
     * Inject a dynamic list of news API services.
     *
     * @param NewsApiInterface[] $apis
     */
    public function __construct(array $apis = [])
    {
        $this->apis = $apis;
    }

    /**
     * Add an API service to the list.
     *
     * @param NewsApiInterface $api
     */
    public function addApi(NewsApiInterface $api): void
    {
        $this->apis[] = $api;
    }

    /**
     * Aggregate articles from all configured APIs.
     *
     * This function calls each news API, retrieves articles,
     * and delegates storage responsibility to the API service itself.
     *
     * @return void
     */
    public function aggregate(): void
    {
        foreach ($this->apis as $apiService) {
            try {
                $articles = $apiService->fetchArticles();

                foreach ($articles as $articleData) {
                    $apiService->storeArticle($articleData);
                }
            } catch (\Throwable $e) {
                logger()->error('AggregatorService failed', [
                    'api' => get_class($apiService),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}