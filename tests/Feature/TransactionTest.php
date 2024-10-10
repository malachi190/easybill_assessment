<?php

namespace Tests\Feature;

use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Create a new transaction.
     */
    public function test_create_new_transaction(): void
    {
        // Generate user
        $user = User::factory()->create();

        // Authenticate user
        $this->actingAs($user);

        // Create transaction data to test
        $data = [
            "transaction_type" => "Phone Bill",
            "amount" => 40.56,
            "status" => "completed",
            "payment_method" => "Bank Transfer",
            "transaction_date" => "2024-05-20 05:20:30", //YYYY-MM-DD HH:MM:SS
            "description" => "Paid phone bill for the month of May"
        ];

        // Send a POST request to a specified endpoint
        $response = $this->postJson('/api/transactions', $data);

        // dd($response->getContent());

        // Assert if response is successful
        $response->assertStatus(201)
            ->assertJson([
                "message" => "Transaction sucessful!",
                "transaction" => [
                    "transaction_type" => "Phone Bill"
                ]
            ]);

        // Assert data was inserted into database
        $this->assertDatabaseHas('transactions', [
            'amount' => 40.56
        ]);
    }

    /**
     * Get all transactions
     * @test
     */
    public function test_get_all_transactions(): void
    {
        // Generate user
        $user = User::factory()->create();

        // Authenticate user
        $this->actingAs($user);

        // Generate transactions for user
        Transactions::factory()->count(5)->create([
            "user_id" => $user->id
        ]);

        // Send GET request to specified endpoint
        $response = $this->getJson('/api/transactions');

        //  dd($response->getContent());

        // Assert if response is successful
        $response->assertStatus(200)
            ->assertJsonCount(5, 'transactions');

        // Assert response contains user_id
        $response->assertJsonFragment([
            "user_id" => $user->id
        ]);
    }

    /**
     * Get single transaction
     * @test
     */
    public function test_get_single_transaction(): void
    {
        // Generate user
        $user = User::factory()->create();

        // Authenticate user
        $this->actingAs($user);

        //  Generate transaction
        $transaction = Transactions::factory()->create([
            "user_id" => $user->id
        ]);

        // Send a Get request to the specified endpoint
        $response = $this->getJson('/api/transactions/' . $transaction->id);

        // dd($response->getContent());

        // Assert if response is successful
        $response->assertStatus(200)
            ->assertJson([
                "message" => "Request successful",
                "transaction" => [
                    "user_id" => $user->id
                ]
            ]);
    }

    /**
     * Update transaction
     * @test
     */
    public function test_update_transaction(): void
    {
        // Generate user
        $user = User::factory()->create();

        // Authenticate user
        $this->actingAs($user);

        //  Generate transaction
        $transaction = Transactions::factory()->create([
            "user_id" => $user->id
        ]);

        // Transation data to update resource
        $data = [
            "transaction_type" => "Electricity Bill",
            "amount" => 69.99,
            "status" => "completed",
            "payment_method" => "Card Payment",
            "transaction_date" => "2024-08-10 05:20:30",
            "description" => "Paid light bill for the month of August"
        ];

        // Send a put request to the specified endpoint
        $response = $this->putJson('/api/transactions/' . $transaction->id, $data);

        // Assert response status
        $response->assertStatus(200)
            ->assertJson([
                "message" => "Transaction Updated",
                "transaction" => [
                    "transaction_type" => "Electricity Bill",
                    "amount" => 69.99
                ]
            ]);

        // Assert the updated data exists in db
        $this->assertDatabaseHas('transactions', [
            "id" => $transaction->id,
        ]);
    }

    /**
     * Update transaction
     * @test
     */
    public function test_delete_transaction(): void
    {
        // Generate user
        $user = User::factory()->create();

        // Authenticate user
        $this->actingAs($user);

        //  Generate transaction
        $transaction = Transactions::factory()->create([
            "user_id" => $user->id
        ]);

        // Send delete request to api endpoint
        $response = $this->deleteJson('/api/transactions/' . $transaction->id);

        // dd($response->getContent());

        // Assert response status
        $response->assertStatus(204);

        // Assert data does not exist in the db
        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id
        ]);
    }
}
