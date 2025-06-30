<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(protected UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Registers a new user and returns user with API token.
     */
    public function register(array $data): array
    {
        $user = $this->userRepo->create($data);
        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Authenticates the user and returns user with token.
     *
     * @throws ValidationException if login fails
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Logs out the authenticated user by deleting all tokens.
     */
    public function logout($user): void
    {
        $user->tokens()->delete();
    }
}