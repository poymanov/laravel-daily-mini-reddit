<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCanBeRendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function testNewUsersCanRegister()
    {
        /** @var User $user */
        $user = User::factory()->make();

        $password = 'password';

        $response = $this->post('/register', [
            'name'                  => $user->name,
            'email'                 => $user->email,
            'username'              => $user->username,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);

        $this->assertDatabaseHas('users', [
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
        ]);
    }
}
