<?php
namespace Tests\Feature;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ArticleTest extends TestCase
{
    use RefreshDatabase;
    public function test_it_returns_articles_list()
    {
        Article::factory()->count(5)->create();
        $response = $this->getJson('/api/articles');
        $response->assertOk()->assertJsonStructure([
            'success',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'content',
                        // We can add more expected fields
                    ]
                ]
            ]
        ]);
    }
    public function test_it_returns_404_for_invalid_article_id()
    {
        $response = $this->getJson('/api/articles/9999');
        $response->assertStatus(404);
    }
}