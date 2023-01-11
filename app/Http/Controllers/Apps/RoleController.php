<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        // GET ROLES
        $roles = Role::when(request()->q, function ($roles) {
            $roles = $roles->where('name', 'like', '%' . request()->q . '%');
        })->with('permissions')->latest()->paginate(5);

        // RENDER WITH INERTIA
        return inertia('Apps/Roles/Index', [
            'roles' => $roles
        ]);
    }


    public function create()
    {
        // GET PERMISSION ALL
        $permissions = Permission::all();

        // RENDER WITH INERTIA
        return inertia('Apps/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        // VALIDATION
        $this->validate($request, [
            'name'          => 'required',
            'permissions'   => 'required'
        ]);

        // CREATE ROLE
        $role = Role::create(['name' => $request->name]);

        // ASSIGN PERMISSIONS TO ROLE
        $role->givePermissionTo($request->permissions);

        // REDIRECT
        return redirect()->route('apps.roles.index');
    }

    public function edit($id)
    {
        // GET ROLE
        $role = Role::with('permissions')->findOrFail($id);

        // GET PERMISSION ALL
        $permissions = Permission::all();

        // RENDER WITH INERTIA
        return inertia('Apps/Roles/Edit', [
            'role'          => $role,
            'permissions'   => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // VALIDATION
        $this->validate($request, [
            'name'          => 'required',
            'permissions'   => 'required'
        ]);

        // UPDATE ROLE
        $role->update(['name' => $request->name]);

        // SYNC PERMISSIONS
        $role->syncPermissions($request->permissions);

        // REDIRECT
        return redirect()->route('apps.roles.index');
    }

    public function destroy($id)
    {
        // FIND ROLE BY ID
        $role = Role::findOrFail($id);

        // DELETE ROLE
        $role->delete();

        // RETURN REDIRECT
        return redirect()->route('apps.roles.index');
    }
}
