<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'author@example.com']);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'author@example.com']);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_reset_password_requires_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'author@example.com']);

        $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertSessionHasErrors('email');

        $this->assertFalse(Hash::check('new-secure-password', $user->fresh()->password));
    }
}
