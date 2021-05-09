<?php

declare(strict_types=1);

namespace Command\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Psy\Command\ExitCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Tests\TestCase;

class AssignAdminRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Выполнение команды без указания email пользователя
     */
    public function testWithoutEmail()
    {
        $this->expectException(RuntimeException::class);
        $this->expectDeprecationMessage('Not enough arguments (missing: "email")');

        $this->artisan('users:assign-admin-role')
            ->assertExitCode(ExitCommand::FAILURE);
    }

    /**
     * Указанный пользователь не существует
     */
    public function testUserNotExisted()
    {
        $this->artisan('users:assign-admin-role test@test.ru')
            ->expectsOutput('User not exists')
            ->assertExitCode(ExitCommand::FAILURE);
    }

    /**
     * Указанный пользователь не подтвердил свой профиль
     */
    public function testUserNotVerified()
    {
        $user = $this->createUser([], true);

        $this->artisan('users:assign-admin-role ' . $user->email)
            ->expectsOutput('User not verified')
            ->assertExitCode(ExitCommand::FAILURE);
    }

    /**
     * Успешное назначение роли
     */
    public function testSuccess()
    {
        $user = $this->createUser();

        $this->artisan('users:assign-admin-role ' . $user->email)
            ->expectsOutput('Success')
            ->assertExitCode(ExitCommand::SUCCESS);
    }
}
