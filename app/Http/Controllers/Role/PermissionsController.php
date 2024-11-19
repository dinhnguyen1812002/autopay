<?php

namespace App\Http\Controllers\Role;

use App\Data\PermissionData;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionsController extends Controller
{
    private function checkRole($role = 'super-admin')
    {
        if (!auth()->user()->hasRole($role)) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function assignPermissions(Request $request, $role)
    {

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findByName($role);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $role->givePermissionTo($request->permissions);

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'role' => $role->name,
            'permissions' => $request->permissions,
        ], Response::HTTP_OK);
    }


    public function index()
    {
        $checkRole = $this->checkRole();
        if (!$checkRole) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }
        $permissions = Permission::all()->map(fn ($permission) => PermissionData::from($permission));

        return response()->json([
            'permissions' => $permissions,
        ], Response::HTTP_OK);
    }
    public function store(PermissionData $permissionData)
    {
        $roleCheck = $this->checkRole('super-admin');
        if ($roleCheck) {
            return $roleCheck;
        }
        $permissionData = Permission::create([
           'name' => $permissionData->name,
           'guard_name' => $permissionData->guard_name,
        ]);
        return response()->json([
            'message' => 'Permission added successfully',
            'permissionData' => permissionData::from($permissionData),
        ], Response::HTTP_CREATED);
    }

    public function update(PermissionData $permissionData, $id)
    {
        $roleCheck = $this->checkRole('super-admin');
        if ($roleCheck) {
            return $roleCheck;
        }
        $permission = Permission::findOrFail($id);
        $permission ->update([
            'name' => $permissionData->name,
            'guard_name' => $permissionData->guard_name,
        ]);
        return response()->json([
           'message' => 'Permission updated successfully',
           'permissionData' => permissionData::from($permission),
        ]);
    }
    public function destroy($id)
    {
        $roleCheck = $this->checkRole('super-admin');
        if ($roleCheck) {
            return $roleCheck;
        }
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json([
            'message' => 'Permission deleted successfully',
        ], Response::HTTP_OK);
    }
}
