<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new user.
     */
    public function createUser(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        ActivityLogger::log('created', $user, "Created user account: {$user->name} ({$user->role})");

        return $user;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(User $user, array $data): User
    {
        // Prevent editing another Super Admin
        if ($user->isSuperAdmin() && $user->id !== auth()->id()) {
            abort(403, 'You cannot edit another Super Admin.');
        }

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

        return $user;
    }
}
