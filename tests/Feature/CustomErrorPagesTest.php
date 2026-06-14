<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_404_page_is_rendered(): void
    {
        $this->get('/missing-page')
            ->assertNotFound()
            ->assertSee('Không tìm thấy nội dung');
    }

    public function test_custom_403_page_is_rendered(): void
    {
        $role = Role::create(['name' => 'author']);
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('admin.posts.index'))
            ->assertForbidden()
            ->assertSee('Bạn không có quyền truy cập trang này');
    }
    public function test_custom_500_page_view_is_renderable(): void
    {
        $this->view('errors.500')
            ->assertSee('500')
            ->assertSee('Hệ thống đang gặp sự cố')
            ->assertDontSee('Stack trace');
    }
}
