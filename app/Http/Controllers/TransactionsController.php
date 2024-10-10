<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Get transactions
            $transactions = Transactions::where("user_id", $user->id)->get();

            // Return response
            return response()->json([
                "message" => "Request Successful",
                "transactions" => TransactionResource::collection($transactions)
            ], 200);
        } catch (\Exception $err) {
            Log::error("An error occured fethcing transactions" . $err->getMessage());

            return response()->json([
                "message" => "An error occured",
                "error" => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request input
            $validator = Validator::make($request->all(), [
                "transaction_type" => "required|string|max:255",
                "amount" => "required|numeric|min:0|max:1000000",
                "status" => "required|string|in:completed,pending,failed",
                "payment_method" => "required|string|max:255",
                "transaction_date" => "required|date_format:Y-m-d H:i:s",
                "description" => "nullable|string|max:1000",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Create transaction
            $transaction = Transactions::create([
                "transaction_type" => $request->transaction_type,
                "amount" => $request->amount,
                "status" => $request->status,
                "payment_method" => $request->payment_method,
                "transaction_date" => $request->transaction_date,
                "description" => $request->description,
                "user_id" => Auth::user()->id
            ]);

            // Return response
            return response()->json([
                "message" => "Transaction sucessful!",
                "transaction" => new TransactionResource($transaction)
            ], 201);
        } catch (\Exception $err) {
            Log::error("An error occured creating transaction" . $err->getMessage());

            return response()->json([
                "message" => "An error occured creating transaction.",
                "error" => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Get transaction
            $transaction = Transactions::where("id", $id)
                ->where("user_id", $user->id)
                ->firstOrFail();

            // Return response
            return response()->json([
                "message" => "Request successful",
                "transaction" => new TransactionResource($transaction)
            ], 200);
        } catch (\Exception $err) {
            Log::error("An error occured fetching transaction" . $err->getMessage());

            return response()->json([
                "message" => "An error occured fetching transaction.",
                "error" => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Get transaction
            $transaction = Transactions::where("id", $id)
                ->where("user_id", $user->id)
                ->firstOrFail();

            // Validate transaction
            $request->validate([
                "transaction_type" => "required|string|max:255",
                "amount" => "required|numeric|min:0|max:1000000",
                "status" => "required|string|in:completed,pending,failed",
                "payment_method" => "required|string|max:255",
                "transaction_date" => "required|date_format:Y-m-d H:i:s",
                "description" => "nullable|string|max:1000",
            ]);

            // Update transaction
            $transaction->update($request->all());

            // Return response
            return response()->json([
                "message" => "Transaction Updated",
                "transaction" => new TransactionResource($transaction)
            ], 200);
        } catch (\Exception $err) {
            Log::error("An error occured updating transaction" . $err->getMessage());

            return response()->json([
                "message" => "An error occured updating transaction.",
                "error" => $err->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Get authenticated user
            $user = Auth::user();

            // Get transaction
            $transaction = Transactions::where("id", $id)
                ->where("user_id", $user->id)
                ->firstOrFail();

            // Delete transaction
            $transaction->delete();

            // Return response
            return response()->json([], 204);
        } catch (\Exception $err) {
            Log::error("Error deleting transaction (ID: $id): " . $err->getMessage());

            return response()->json([
                "message" => "An error occured deleting transaction",
                "error" => $err->getMessage()
            ], 500);
        }
    }
}
