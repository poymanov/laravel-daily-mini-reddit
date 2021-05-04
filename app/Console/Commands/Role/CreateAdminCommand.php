<?php

namespace App\Console\Commands\Role;

use Illuminate\Console\Command;
use Psy\Command\ExitCommand;
use Spatie\Permission\Models\Role;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (Role::create(['name' => 'admin'])) {
            return ExitCommand::SUCCESS;
        } else {
            return ExitCommand::FAILURE;
        }
    }
}
