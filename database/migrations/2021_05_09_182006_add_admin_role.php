<?php

use Illuminate\Database\Migrations\Migration;

class AddAdminRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->isRoleExists()) {
            $now = now();
            DB::table('roles')->insert([
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('roles')->delete();
    }

    /**
     * Проверка существования роли администратора
     *
     * @return bool
     */
    private function isRoleExists(): bool
    {
        return DB::table('roles')->where('name', 'admin')->exists();
    }
}
