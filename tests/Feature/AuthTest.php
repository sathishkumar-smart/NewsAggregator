<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    public function test_user_can_register_and_login()
    {
        $this->setUpFaker();

        $response = $this->postJson('/api/user/register', [
            'name' => 'Test User',
            'email' => 'user_' . uniqid() . '@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user', 'token']);
    }
}