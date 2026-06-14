<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_registered_user_must_verify_email(): void
    {
        Notification::fake();
        Role::create(['name' => 'author']);

        $this->post(route('register.store'), [
            'name' => 'New Author',
            'email' => 'new-author@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'new-author@example.com')->firstOrFail();

        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_is_redirected_from_dashboard_to_verification_notice(): void
    {
        Role::create(['name' => 'author']);
        $user = User::factory()->unverified()->create();
        $user->assignRole('author');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_user_can_verify_email_with_signed_link(): void
    {
        Role::create(['name' => 'author']);
        $user = User::factory()->unverified()->create();
        $user->assignRole('author');

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_can_resend_verification_email(): void
    {
        Notification::fake();
        Role::create(['name' => 'author']);
        $user = User::factory()->unverified()->create();
        $user->assignRole('author');

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
