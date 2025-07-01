<?php
namespace Tests\Feature;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_save_preferences()
    {
        $user = User::factory()->create();
        $sources = Source::factory()->count(2)->create();
        $categories = Category::factory()->count(2)->create();
        $authors = Author::factory()->count(2)->create();

        $response = $this->actingAs($user)->postJson('/api/preferences', [
            'sources' => $sources->pluck('id')->toArray(),
            'categories' => $categories->pluck('id')->toArray(),
            'authors' => $authors->pluck('id')->toArray(),
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}