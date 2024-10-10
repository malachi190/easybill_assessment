<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::all();

            return response()->json([
                "message" => "Request Successfull",
                "data" => UserResource::collection($users)
            ], 200);
        } catch (\Exception $err) {
            Log::error("Error fetching users: " . $err->getMessage());

            return response()->json([
                "message" => "Failed to fetch users.",
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
            // Validating inputs
            $validator = Validator::make($request->all(), [
                "name" => "required|string|max:255",
                "email" => "required|email|unique:users,email|string|max:255",
                "password" => "required|min:8"
            ]);

            // Return an error response if validation fails
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // User Creation
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);

            // Return response once user is created
            return response()->json([
                "message" => "User created!",
                "user" => new UserResource($user)
            ], 201);
        } catch (\Exception $err) {
            Log::error("Error creating user: " . $err->getMessage());

            return response()->json([
                "message" => "Failed to create user.",
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
            $user = User::findOrFail($id);

            return response()->json([
                "user" => new UserResource($user)
            ], 200);
        } catch (\Exception $err) {
            Log::error("Error fetching user (ID: $id): " . $err->getMessage());

            return response()->json([
                "message" => "An error occured fetching user",
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
            // Find user
            $user = User::findOrFail($id);

            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            // Update user details
            $user->update($request->only(['name', 'email']));

            return response()->json([
                "message" => "Request Successfull!",
                "user" => new UserResource($user)
            ], 200);
        } catch (\Exception $err) {
            Log::error("Error updating user info (ID: $id): " . $err->getMessage());

            return response()->json([
                "message" => "An error occured updating user info",
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
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([], 204);
        } catch (\Exception $err) {
            Log::error("Error deleting user info (ID: $id): " . $err->getMessage());

            return response()->json([
                "message" => "An error occured deleting user info",
                "error" => $err->getMessage()
            ], 500);
        }
    }
}
