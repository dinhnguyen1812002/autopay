<?php

namespace App\Http\Controllers\Auth;

use App\Data\LoginData;
use App\Data\UpdateUserData;
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

        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $user->assignRole('super-admin');
        Auth::login($user);

        return response()->json([
            'message' => 'Register successfully',
            'user' => $user,
            'role' => $role,
        ], Response::HTTP_CREATED);
    }


    public function login(LoginData $data)
    {

        if (!Auth::attempt([
            'email' => $data->email,
            'password' => $data->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
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
    public function update(UpdateUserData $userData, Request $request, $ulid)
    {
        $currentUser = Auth::user();

        // Kiểm tra nếu người dùng hiện tại đang cố gắng cập nhật thông tin của chính họ
        if ($currentUser->id !== $user->id) {
            return response()->json(['message' => 'You can only update your own information.'],
                Response::HTTP_FORBIDDEN);
        }

        // Tìm kiếm người dùng bằng ULID
        $user = User::where('id', $ulid)->firstOrFail();

        // Cập nhật avatar nếu có trong request
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        // Cập nhật các thông tin cơ bản
        $user->name = $userData->name;
        $user->email = $userData->email;

        // Kiểm tra nếu người dùng muốn thay đổi mật khẩu
        if (!empty($userData->password)) {
            $user->password = Hash::make($userData->password);
        }

        // Lưu thông tin cập nhật
        $user->save();

        return response()->json([
            'message' => 'User information updated successfully.',
            'user' => $user,
        ], Response::HTTP_OK);
    }

    public function getUserInfo()
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json(['message' => 'Unauthorized'],
                Response::HTTP_UNAUTHORIZED);
        }

        $userData = new UserData(
            name: $currentUser->name,
            email: $currentUser->email,
            email_verified_at: $currentUser->email_verified_at,
            roles: $currentUser->roles->pluck('name')->toArray(),
            permissions: $currentUser->getAllPermissions()->pluck('name')->toArray()
        );

        return response()->json([
            'message' => 'User information retrieved successfully.',
            'user' => $userData,
        ], Response::HTTP_OK);
    }

}
