<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AccessController extends Controller {

    public function __construct() {

    }


    public function index() {

        $roles = Role::all();

        $components = [
            'memo',
            'user',
        ];

        return view("app.role-permission.index", [
            'roles' => $roles,
            'components' => $components,
        ]);

    }


    public function getRolePermissions($role) {

        $role = Role::where('name', $role)->firstOrFail();
        $permissions = $role->permissions->pluck('name');

        return response()->json([
            'status' => 'success',
            'permissions' => $permissions
        ]);

    }


    public function updatePermission(Request $request) {

        $data = $request->input('params');

        $roleName = isset($data['role_name']) ? $data['role_name'] : null;
        $permissions = isset($data['permissions']) ? $data['permissions'] : null;

        if (!$roleName) {
            return response()->json(['status' => 'error', 'message' => 'Role name is missing.']);
        }

        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            return response()->json(['status' => 'error', 'message' => 'Role not found.']);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $role->syncPermissions($permissions);

        return response()->json(['status' => 'success', 'message' => 'Permissions updated successfully.']);

    }

}
