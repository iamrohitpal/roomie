<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Roomie');
    }

    public function test_user_can_request_otp()
    {
        $response = $this->post('/login', [
            'phone' => '1234567890'
        ]);

        $response->assertRedirect(route('auth.verify-otp'));
        $this->assertDatabaseHas('users', [
            'phone' => '1234567890'
        ]);
        
        $user = User::where('phone', '1234567890')->first();
        $this->assertNotNull($user->otp);
    }

    public function test_invalid_phone_number_fails_validation()
    {
        $response = $this->post('/login', [
            'phone' => '123'
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_user_can_verify_otp_and_login()
    {
        $user = User::create([
            'phone' => '9876543210',
            'otp' => '123456',
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        session(['auth_phone' => '9876543210']);

        $response = $this->post('/verify-otp', [
            'otp' => '123456'
        ]);

        $response->assertRedirect(route('auth.profile-setup'));
        $this->assertAuthenticatedAs($user);
    }
}
