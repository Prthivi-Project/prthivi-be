<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterAccountTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_new_account_with_invalid_data()
    {
        $response = $this->post('/api/v1/register', [
            "email" => "aksdjakjda",
            "name" => 1,
            "password" => '123',
            "password_confirmation" => "1234"
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath("meta.status", "error");
        $response->assertJson(function (AssertableJson $json) {
            $json->has("error")
                ->etc();
        });
    }

    public function test_create_new_customer_account_with_valid_data()
    {
        $this->seed();
        $payload = [
            'name' => "valid name",
            'email' => "validEmail@gmail.com",
            'password' => "password123",
            'password_confirmation' => "password123",
            'phone' => "089123123323",
        ];
        $response = $this->post("api/v1/register", $payload);

        $response->assertStatus(201);
        $response->assertJsonPath("meta.status", "success");
        $response->assertJsonPath("data.user.name", $payload["name"]);
        $response->assertJsonPath("data.user.email", $payload["email"]);
        $response->assertJsonMissing(["password"]);
    }
}
