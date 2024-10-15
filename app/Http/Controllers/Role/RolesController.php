<?php

namespace App\Http\Controllers\Role;

use App\Data\RoleData;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RolesController extends Controller
{
    public function store(RoleData $roleData)
    {
        // Check if the user has the 'super-admin' role
        if (!auth()->user()->hasRole('super-admin')) {
            return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            // Try to create the role
            $role = Role::create([
                'ulid' => (string) Str::ulid(), // Generates a unique ULID
                'name' => $roleData->name,
            ]);

            // Sync permissions if provided
            if (!empty($roleData->permissions)) {
                $permissions = Permission::whereIn('name', $roleData->permissions)->get();
                $role->syncPermissions($permissions);
            }

            // Return success response with the role and its permissions
            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ], Response::HTTP_CREATED);

        } catch (RoleAlreadyExists $e) {

            return response()->json([
                'message' => "A role '{$roleData->name}' already exists for guard 'sanctum'."
            ], Response::HTTP_CONFLICT);
        }
    }


}
