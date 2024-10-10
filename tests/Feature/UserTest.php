<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation.
     * @test
     */
    public function test_user_creation(): void
    {
        // Create data to test
        $data = [
            "name" => "Harry Oswald",
            "email" => "harryoswald@gmail.com",
            "password" => "password1234",
        ];

        // Send post request to specified endpoint
        $response = $this->postJson("/api/users", $data);

        // dd($response->getContent());

        // Assert if response is successful
        $response->assertStatus(201)
            ->assertJson([
                "message" => "User created!",
                "user" => [
                    "name" => "Harry Oswald"
                ]
            ]);


        // Assert data was inserted into database
        $this->assertDatabaseHas('users', [
            'email' => 'harryoswald@gmail.com'
        ]);
    }

    /**
     * Test getting a single user by id
     * @test
     * @return void
     */
    public function test_get_user_by_id(): void
    {
        // Generate a random user
        $user = User::factory()->create();

        // Send Get request to api endpoint
        $response = $this->getJson(uri: '/api/users/' . $user->id);

        // dd($response->getContent());

        // Assert status and json response
        $response->assertStatus(200)
            ->assertJson([
                "user" => [
                    "id" => $user->id,
                    "name" => $user->name
                ]
            ]);
    }

    /**
     * Test getting all users
     * @test
     * @return void
     */
    public function test_get_all_users(): void
    {
        // Generate random users
        $users = User::factory()->count(5)->create();

        // Send Get request to api endpoint
        $response = $this->getJson(uri: '/api/users');

        // Assert status and json response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ])
            ->assertJson([
                'message' => 'Request Successfull',
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                })->toArray(),
            ]);
    }

    /**
     * Test updating user details
     * @test
     * @return void
     */
    public function test_update_user_information(): void
    {
        // Generate random user
        $user = User::factory()->create();

        // Defining data to update user
        $data = [
            "name" => "Emmanuel K",
            "email" => "emmanuel@gmail.com",
        ];

        // Send a PUT request to the api endpoint
        $response = $this->putJson('/api/users/' . $user->id, $data);

        // dd($response->getContent());

        // Assert response status
        $response->assertStatus(200)
            ->assertJson([
                "message" => "Request Successfull!",
                "user" => [
                    "name" => "Emmanuel K",
                    "email" => "emmanuel@gmail.com",
                ]
            ]);

        // Assert the updated data exists in db
        $this->assertDatabaseHas('users', [
            "id" => $user->id,
            "name" => "Emmanuel K",
            "email" => "emmanuel@gmail.com",
        ]);
    }

    /**
     * Test deleting user
     * @test
     * @return void
     */
    public function test_delete_user(): void
    {
        // Generate random user
        $user = User::factory()->create();

        // Send delete request to api endpoint
        $response = $this->deleteJson('/api/users/' . $user->id);

        // dd($response->getContent());

        // Assert response status
        $response->assertStatus(204);

        // Assert data does not exist in the db
        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
