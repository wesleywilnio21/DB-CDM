<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeSuperAdmin();
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        $data     = $request->validated();
        $user     = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        ActivityLogger::log('created', $user, "Created user account: {$user->name} ({$user->role})");

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        // Cegah edit Super Admin lain
        if ($user->isSuperAdmin() && $user->id !== auth()->id()) {
            abort(403, 'You cannot edit another Super Admin.');
        }

        $data    = $request->validated();
        $oldRole = $user->role;

        $user->update(array_filter([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'role'     => $data['role'],
        ], fn ($v) => $v !== null));

        if (filled($data['password'] ?? null)) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        ActivityLogger::log('updated', $user, "Updated user account: {$user->name}", [
            'old_role' => $oldRole,
            'new_role' => $user->role,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('deleted', $user, "Deleted user account: {$name}");

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
