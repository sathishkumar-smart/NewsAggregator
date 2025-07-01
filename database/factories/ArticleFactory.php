<?php

namespace Database\Factories;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
class ArticleFactory extends Factory
{
    protected $model = Article::class;
    public function definition()
    {
        return ['title' => $this->faker->sentence, 'content' => $this->faker->paragraph, 'published_at' => $this->faker->dateTime, 'source_id' => Source::factory(), 'category_id' => Category::factory(), 'author_id' => Author::factory(), 'url' => $this->faker->unique()->url,];
    }
}