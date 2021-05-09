<?php

declare(strict_types=1);

namespace App\Console\Commands\User;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Psy\Command\ExitCommand;

class AssignAdminRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-admin-role {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign admin role to user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::firstWhere('email', $this->argument('email'));

        if (!$user) {
            $this->error('User not exists');
            return ExitCommand::FAILURE;
        }

        if (!$user->hasVerifiedEmail()) {
            $this->error('User not verified');
            return ExitCommand::FAILURE;
        }

        $user->assignRole(RoleEnum::ADMIN);

        $this->info('Success');

        return ExitCommand::SUCCESS;
    }
}
