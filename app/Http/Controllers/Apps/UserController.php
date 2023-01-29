<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        // GET USERS
        $users = User::when(request()->q, function ($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->with('roles')->latest()->paginate(5);

        // RETURN INERTIA
        return Inertia::render('Apps/Users/Index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        // GET ROLES
        $roles = Role::all();

        return Inertia::render('Apps/Users/Create', [
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        // VALIDATE REQUEST
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|unique:users',
            'password'  => 'required|confirmed'
        ]);

        // CREATE USER
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => $request->password
        ]);

        // ASSIGN ROLES TO USER
        $user->assignRole($request->roles);

        // REDIRECT
        return redirect()->route('apps.users.index');
    }

    public function edit($id)
    {
        // GET USER
        $user = User::with('roles')->findOrFail($id);

        // GET ROLES
        $roles = Role::all();

        // RETURN INERTIA
        return Inertia::render('Apps/Users/Edit', [
            'user'  => $user,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name'      => 'required',
            'email'     => 'required|unique:users,email,' . $user->id,
            'password'  =>  'nullable|confirmed'
        ]);

        // CHECK PASSWORD IF EMPTY
        if ($request->password == '') {
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);
        } else {
            $user->update([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => bcrypt($request->password)
            ]);
        }

        // ASSIGN ROLES TO USER
        $user->syncRoles($request->roles);

        // REDIRECT
        return redirect()->route('apps.users.index');
    }

    public function destroy($id)
    {
        // FIND USER
        $user = User::findOrFail($id);

        // DELETE USER
        $user->delete();

        // REDIRECT
        return redirect()->route('apps.users.index');
    }
}
