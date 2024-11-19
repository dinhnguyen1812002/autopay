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
    public function register(UserData $userData, Request $request)
    {
        if (User::where('email', $userData->email)->exists()) {
            return response()->json(['message' => 'Email already exit'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        } else {
            $avatarPath = './storage/avatars/default_avatar.png';
        }

        $role = Role::where('name', 'super-admin')->where('guard_name', 'sanctum')->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found for sanctum guard'], Response::HTTP_NOT_FOUND);
        }
        $user = User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'email_verified_at' => $userData->email_verified_at,
            'avatar' => $avatarPath,
        ]);
        $user->assignRole($role->name);

        return response()->json([
            'message' => 'Register successfully',
            'user' => $user->name,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),

        ], Response::HTTP_CREATED);
    }

    //    public function register(UserData $userData)
    //    {
    //        // Tạo người dùng mới với dữ liệu từ UserData
    //        $user = User::create([
    //            'name' => $userData->name,
    //            'email' => $userData->email,
    //            'password' => Hash::make($userData->password),
    //            'email_verified_at' => $userData->email_verified_at,  // Nếu có giá trị
    //        ]);
    //
    //        // Gán roles cho người dùng nếu có
    //        if (!empty($userData->roles)) {
    //            $user->syncRoles($userData->roles);
    //        }
    //
    //        // Gán permissions cho người dùng nếu có
    //        if (!empty($userData->permissions)) {
    //            $user->syncPermissions($userData->permissions);
    //        }
    //
    //        // Tự động đăng nhập người dùng sau khi đăng ký thành công
    //        Auth::login($user);
    //
    //        // Trả về thông tin người dùng đã đăng ký thành công
    //        return response()->json([
    //            'message' => 'Register successfully',
    //            'user' => $user,
    //            'roles' => $user->roles->pluck('name'),  // Trả về danh sách roles
    //            'permissions' => $user->getAllPermissions()->pluck('name'),  // Trả về danh sách permissions
    //        ], Response::HTTP_CREATED);
    //    }
    public function login(LoginData $data, Request $request)
    {

        $user = User::where('email', $data->email)->first();


        if (!$user || !Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid login credentials.'],
            ]);
        }
        $avatarUrl = $user->avatar ? asset('storage/' . $user->avatar) : asset('storage/avatars/default_avatar.png');


        $token = $user->createToken('auth_token')->plainTextToken;
        $roles = $user->getRoleNames();
        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $this->getEmail($user),
                'avatar' => $avatarUrl,
                'roles' => $roles,
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
        $user = User::where('id', $ulid)->firstOrFail();
        // Kiểm tra nếu người dùng hiện tại đang cố gắng cập nhật thông tin của chính họ
        if ($currentUser->id !== $user->id) {
            return response()->json(
                ['message' => 'You can only update your own information.'],
                Response::HTTP_FORBIDDEN
            );
        }


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
            return response()->json(
                ['message' => 'Unauthorized'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $userData = new UserData(
            name: $currentUser->name,
            email: $currentUser->email,
            password: $currentUser->password,
            email_verified_at: $currentUser->email_verified_at,
            roles: $currentUser->roles->pluck('name')->toArray(),
            permissions: $currentUser->getAllPermissions()->pluck('name')->toArray()
        );
        return response()->json([
            'message' => 'User information retrieved successfully.',
            'user' => $userData,
        ], Response::HTTP_OK);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getEmail($user)
    {
        return $user->email;
    }

}
