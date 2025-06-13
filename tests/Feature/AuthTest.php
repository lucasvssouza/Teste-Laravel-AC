<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name'                  => 'João',
            'email'                 => 'joao@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertJson(['status' => 'success']);
        $this->assertAuthenticatedAs($user);
    }
}
