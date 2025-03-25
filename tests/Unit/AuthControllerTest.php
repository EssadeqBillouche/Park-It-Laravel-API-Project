<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController();
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $request = new LoginUserRequest([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response = $this->controller->login($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->original['data']);
    }

    public function test_login_with_invalid_credentials()
    {
        $request = new LoginUserRequest([
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword'
        ]);

        $response = $this->controller->login($request);
        
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_register_creates_new_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new StoreUserRequest($userData);
        $response = $this->controller->register($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function test_register_fails_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $request = new StoreUserRequest($userData);
        $response = $this->controller->register($request);
        
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function test_logout_deletes_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        
        Sanctum::actingAs($user);
        
        $request = Request::create('/api/logout', 'POST');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->logout($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id
        ]);
    }
}
