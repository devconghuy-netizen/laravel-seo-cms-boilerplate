<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InactiveAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_login(): void
    {
        Role::create(['name' => 'author']);
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);
        $user->assignRole('author');

        $this->post(route('login.store'), [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_logged_in_user_is_logged_out_from_protected_routes(): void
    {
        Role::create(['name' => 'author']);
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('author');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
