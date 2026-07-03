<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();
        $users = User::latest()->paginate(10);
        $roles = Role::all();
        return view('users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Only super_admin can create super_admin
        if ($request->role === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['role' => 'Only Super Admins can assign super_admin role.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $user->assignRole($request->role);

        ActivityLogger::log('created', $user, "Created user account: {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Permission check
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
             abort(403, 'You cannot edit a Super Admin.');
        }

        if ($request->role === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors(['role' => 'Only Super Admins can assign super_admin role.']);
        }

        $oldRole = $user->role;
        $user->update($request->only(['name', 'email', 'role']));
        
        // Sync spatie role
        $user->syncRoles([$request->role]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        ActivityLogger::log('updated', $user, "Updated user account: {$user->name}", [
            'old_role' => $oldRole,
            'new_role' => $user->role
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin()) {
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

    protected function authorizeAdmin()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
