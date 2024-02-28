<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Run migrations
        Artisan::call('migrate');

        // Run Passport migrations
        Artisan::call('passport:install');
    }

    /** @test */
    public function it_can_login_with_valid_credentials(): void
    {
        // Create a user for testing
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('test-password'),
        ]);

        // Attempt to login
        $response = $this->postJson('api/login', [
            'email' => 'test@example.com',
            'password' => 'test-password',
        ]);

        // Assert successful login
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token', 'name'],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'User login successful.',
            ]);
    }

    /** @test */
    public function it_fails_to_login_with_invalid_credentials(): void
    {
        // Attempt to login with invalid credentials
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'invalid-password',
        ]);

        // Assert failed login
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => ['error' => 'Invalid credentials'],
            ]);
    }

    /** @test */
    public function it_requires_email_and_password(): void
    {
        // Attempt to login without providing email and password
        $response = $this->postJson('/api/login');

        // Assert validation error
        $response->assertStatus(422)
            ->assertJson([
                "success" => false,
                "message" => "Validation Error",
                "data" => [
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."]
                ]
            ]);
    }

    /** @test */
    public function it_throttles_login_requests(): void
    {
        // Creating a user to use for login attempts
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('test-password'),
        ]);

        // Simulating 6 login attempts within 1 minute
        for ($i = 0; $i <= 6; $i++) {
            $response = $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'test-password',
            ]);

            if ($i <= 5) {
                $response->assertStatus(200)
                    ->assertJson(['message' => 'User login successful.']);
            } else {
                // The 6th attempt should be throttled
                $response->assertStatus(429);
            }
        }
    }

}
