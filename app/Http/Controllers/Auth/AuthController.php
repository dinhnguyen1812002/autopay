<?php

namespace App\Http\Controllers\Auth;

use App\Data\LoginData;
use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(UserData $userData)
    {
        $user = User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
        ]);

        Auth::login($user);
        return response()->json(
            [
            'message' => 'Register successfully',
        ],
            Response::HTTP_CREATED
        );
    }

    public function login(LoginData $data)
    {
        // Attempt to log the user in using the provided credentials
        if (!Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Create a token for the user (you can customize the token name)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token along with a success message
        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
        ], Response::HTTP_OK);
    }
    public function logout(UserData $request)
    {
        // Xóa token hiện tại của user
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}
