<?php

namespace App\Console\Commands;

use App\Services\AggregatorService;
use App\Services\GuardianApiService;
use App\Services\NewsApiService;
use App\Services\NewYorkTimesApiService;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store articles from multiple news APIs';

    public function __construct(
        protected AggregatorService $aggregator
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Create instances (ideally inject via service container)
        $aggregator = new AggregatorService([
            new NewsApiService(),
            new NewYorkTimesApiService(),
            new GuardianApiService(),
        ]);

        $aggregator->aggregate();
    }
}
