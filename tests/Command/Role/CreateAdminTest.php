<?php

declare(strict_types=1);

namespace Command\Role;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Tests\TestCase;

class CreateAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Успешное создание роли
     */
    public function testSuccess()
    {
        $this->artisan('role:create-admin')->assertExitCode(0);

        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    /**
     * Попытка создания, когда роль уже существует
     */
    public function testExisted()
    {
        $this->artisan('role:create-admin');
        $this->expectException(RoleAlreadyExists::class);
        $this->artisan('role:create-admin')->assertExitCode(1);
    }
}
