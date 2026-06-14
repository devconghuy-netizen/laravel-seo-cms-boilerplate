<?php

namespace Tests\Feature;

use App\Models\AffiliateLink;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_attempts_are_rate_limited(): void
    {
        Role::create(['name' => 'author']);
        User::factory()->create([
            'email' => 'author@example.com',
            'password' => Hash::make('password123'),
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'author@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'author@example.com',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_register_requests_are_rate_limited(): void
    {
        Role::create(['name' => 'author']);

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->post(route('register.store'), [
                'name' => "Author {$attempt}",
                'email' => "author{$attempt}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])->assertRedirect(route('verification.notice'));

            auth()->logout();
        }

        $this->post(route('register.store'), [
            'name' => 'Blocked Author',
            'email' => 'blocked@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertTooManyRequests();
    }

    public function test_password_reset_email_requests_are_rate_limited(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'author@example.com']);

        $this->post(route('password.email'), [
            'email' => 'author@example.com',
        ])->assertSessionHas('status');

        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->post(route('password.email'), [
                'email' => 'author@example.com',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('password.email'), [
            'email' => 'author@example.com',
        ])->assertTooManyRequests();
    }

    public function test_affiliate_redirect_is_rate_limited(): void
    {
        $product = AffiliateLink::create([
            'title' => 'Demo product',
            'url' => 'https://example.com/product',
            'slug' => 'demo-product',
            'affiliate_program' => 'Demo',
            'type' => 'product',
            'is_active' => true,
        ]);

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->get(route('affiliate.redirect', $product->slug))
                ->assertRedirect('https://example.com/product');
        }

        $this->get(route('affiliate.redirect', $product->slug))
            ->assertTooManyRequests();
    }
}
