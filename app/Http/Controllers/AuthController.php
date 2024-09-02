<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('Client');

            return response()->json([
                'status' => 200,
                'message' => 'User registered successfully.',
                'data' => $user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to register user.',
                'data' => ['error' => $th->getMessage()]
            ], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'message' => 'Successfully logged in.',
                'data' => ['token' => $token]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed.',
                'data' => ['errors' => $e->errors()]
            ], 400);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to login.',
                'data' => ['error' => $th->getMessage()]
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Logged out successfully.',
                'data' => []
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }
    }
}
