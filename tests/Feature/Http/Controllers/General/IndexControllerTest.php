<?php

namespace Tests\Feature\Http\Controllers\General;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function test_redirect_login_when_not_authenticated()
    {
        $response = $this->followingRedirects()->get('/')->assertStatus(200);

        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_redirect_dashboard_when_authenticated()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::find(1);

        $this->actingAs($user);

        $response = $this->followingRedirects()->get('/')->assertStatus(200);

        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function test_can_view_dashboard()
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        $user = User::find(1);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertSuccessful();
        $response->assertViewIs('app.dashboard');
    }
}
