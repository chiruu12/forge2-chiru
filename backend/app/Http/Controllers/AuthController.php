<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'organization_name' => ['required', 'string', 'max:255'],
        ]);

        $organization = Organization::create([
            'name' => $validated['organization_name'],
            'slug' => \Illuminate\Support\Str::slug($validated['organization_name']) . '-' . uniqid(),
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $organization->id,
            'role' => 'admin',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('organization'),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('organization'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->load('organization'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->where('id', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(null, 204);
    }
}
