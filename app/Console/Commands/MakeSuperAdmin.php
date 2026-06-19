<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'make:super-admin {email}';

    protected $description = 'Promote a user to super_admin role';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("User with email {$email} not found.");

            return 1;
        }

        $user->update(['role' => 'super_admin']);
        $this->info("User {$user->name} ({$email}) has been promoted to super_admin.");

        return 0;
    }
}
