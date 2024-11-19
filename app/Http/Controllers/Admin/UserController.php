<?php

namespace App\Http\Controllers\Admin;

use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'User not logged in.'], Response::HTTP_UNAUTHORIZED);
        }
        if (!Auth::user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        $users = User::with(['roles', 'permissions'])->get()->map(function ($user) {
            $avatarUrl = $user->avatar
                ? asset('storage/' . $user->avatar)
                : asset('storage/avatars/default_avatar.png');

            return new UserData(
                name: $user->name,
                email: $user->email,
                password: '',
                email_verified_at: $user->email_verified_at,
                roles: $user->roles->pluck('name')->toArray(),
                permissions: $user->permissions->pluck('name')->toArray(),
                avatarUrl: $avatarUrl
            );
        });


        return response()->json([
            'message' => 'List of all users',
            'users' => $users,
        ], Response::HTTP_OK);
    }

    public function store(UserData $userData, Request $request)
    {
        if (!Auth::user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        // Xử lý upload avatar
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        } else {
            $avatarPath = './storage/avatars/default_avatar.png';
        }
        // Tạo người dùng mới
        $user = User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'avatar' => $avatarPath,
            'is_active' => true,
        ]);

        if (!empty($userData->roles)) {
            $roles = Role::whereIn('name', $userData->roles)->get();
            $user->assignRole($roles);
        }

        // Gán quyền cho người dùng
        if (!empty($userData->permissions)) {
            $permissions = Permission::whereIn('name', $userData->permissions)->get();
            $user->givePermissionTo($permissions);
        }
        return response()->json([
            'message' => 'User created and roles/permissions assigned successfully.',
            'user' => $user->load('roles', 'permissions'),
        ], Response::HTTP_CREATED);
    }
}
