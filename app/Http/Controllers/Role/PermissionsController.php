<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionsController extends Controller
{
    public function assignPermissions(Request $request, $role)
    {
        // Xác thực dữ liệu
        $request->validate([
            'permissions' => 'required|array', // Dữ liệu phải là một mảng quyền
            'permissions.*' => 'string|exists:permissions,name', // Mỗi quyền phải tồn tại trong bảng permissions
        ]);

        // Tìm vai trò theo tên
        $role = Role::findByName($role);

        //Nếu không tìm thấy vai trò
        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        // Cấp quyền cho vai trò
        $role->givePermissionTo($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'role' => $role->name,
            'permissions' => $request->permissions,
        ], Response::HTTP_OK);
    }

}
