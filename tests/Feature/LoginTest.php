<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_view_a_login_form()
    {
        $response = $this->get(route('login'));

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_user_can_not_login_with_incorrect_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'username' => $user->username,
            'password' => 'password-wrong',
        ]);

        $response->assertRedirect(route('root'));
        $response->assertSessionHasErrors('username');
        $this->assertTrue(session()->hasOldInput('username'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    /** @test */
    public function test_user_can_logout()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('root'));
        $this->assertGuest();
    }
}
