<?php

namespace App\Http\Controllers\Auth;

use App\Data\LoginData;
use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
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
        $role = Role::where('name', 'super-admin')->first();
        if ($role) {
            $user->assignRole('super-admin');
        } else {
            return response()->json("not found role: ");
        }

        Auth::login($user);

        return response()->json(
            [
                'message' => 'Register successfully',
                'user' => $user,      // Return the user object
                'role' => $role,      // Return the assigned role
            ],
            Response::HTTP_CREATED
        );
    }

    public function login(LoginData $data)
    {

        if (!Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;


        $roles = $user->getRoleNames();


        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles, // Trả về danh sách vai trò
            ],
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        // Xóa token hiện tại của user
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }



}
