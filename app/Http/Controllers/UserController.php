<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:super_admin,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        ActivityLogger::log('created', $user, "Created user account: {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->id],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'in:super_admin,admin'],
        ]);

        // Permission check (only Super Admin can access this method now, but prevent editing other Super Admins if needed)
        if ($user->isSuperAdmin() && $user->id !== auth()->id()) {
            abort(403, 'You cannot edit another Super Admin.');
        }

        $oldRole = $user->role;
        $user->update($request->only(['name', 'username', 'email', 'role']));

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        ActivityLogger::log('updated', $user, "Updated user account: {$user->name}", [
            'old_role' => $oldRole,
            'new_role' => $user->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Admins can delete users.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('deleted', $user, "Deleted user account: {$name}");

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    protected function authorizeSuperAdmin()
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
