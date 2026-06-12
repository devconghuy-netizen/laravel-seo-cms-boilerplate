<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@affipress.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@affipress.test'],
            [
                'name' => 'Editor User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $editor->assignRole('editor');

        // Create author users
        for ($i = 1; $i <= 3; $i++) {
            $author = User::firstOrCreate(
                ['email' => "author{$i}@affipress.test"],
                [
                    'name' => "Author {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $author->assignRole('author');
        }
    }
}
