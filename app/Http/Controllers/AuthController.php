<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    public function login(LoginUserRequest $request)
    {
        $validated = $request->all();

        if (!Auth::attempt($validated)) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'Success',
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken
            ]
        ], 200);
    }

    public function register(StoreUserRequest $request)
    {
        try {
            $validated = $request->all();
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'status' => 'Success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => $user,
                    'token' => $user->createToken('API Token')->plainTextToken
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'User registration failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'status' => 'Success',
                'message' => 'Successfully logged out',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'message' => 'Logout failed: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
