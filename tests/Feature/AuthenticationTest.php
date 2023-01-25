<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use function Termwind\render;

class AuthenticationTest extends TestCase
{

    use RefreshDatabase;

    public function test_authentication_with_invalid_credentials()
    {
        $response = $this->post('/api/v1/login', ["email" => "invalidco", "password" => "aksdsjdaksj"]);
        $response->assertStatus(422);
        $response->assertJsonPath("meta.status", "error");
    }

    public function test_authentication_with_valid_credentials()
    {
        User::factory()->count(1)->state(new Sequence([
            "email" => "triadi@gmail.com"
        ]))->create();

        $response = $this->post('/api/v1/login', ["email" => "triadi@gmail.com", "password" => 'password']);

        $response->assertOk();

        $response->assertJsonPath("meta.status", "success");
        $response->assertJsonPath("meta.status", "success");
    }

    public function test_get_current_authenticated_user()
    {
        $user = User::create([
            "email" => "validemail@gmail.com",
            "name" => "validname123",
            "password" => Hash::make("password123"),
            "phone" => fake()->phoneNumber(),
            "role_id" => 3,
        ]);

        $token = $user->createToken("device")->plainTextToken;

        $response = $this->get('/api/v1/me', [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);

        $response->assertOk();

        $response->assertJsonPath("meta.status", "success");
        $response->assertJsonPath("data.user.email", "triadi@gmail.com");
    }
}
