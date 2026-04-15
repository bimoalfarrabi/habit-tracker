<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\WelcomePageContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($user)->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_user_can_access_admin_pages(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_user_from_admin_panel(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'role' => User::ROLE_USER,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.com',
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_admin_can_update_welcome_cms_content(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        WelcomePageContent::singleton();

        $payload = [
            'hero_badge' => 'Badge Baru',
            'hero_title' => 'Judul Hero Baru',
            'hero_highlight' => 'Highlight Baru',
            'hero_description' => 'Deskripsi hero baru',
            'hero_primary_cta_text' => 'CTA Primer',
            'hero_secondary_cta_text' => 'CTA Sekunder',
            'preview_title' => 'Preview Baru',
            'preview_description' => 'Deskripsi preview baru',
            'stories_title' => 'Stories Baru',
            'stories_description' => 'Deskripsi stories baru',
            'features_title' => 'Fitur Baru',
            'how_it_works_title' => 'Cara Kerja Baru',
            'final_cta_title' => 'Final CTA Baru',
            'final_cta_description' => 'Deskripsi CTA baru',
            'footer_note' => 'Footer Note Baru',
        ];

        $response = $this->actingAs($admin)->put(route('admin.welcome-content.update'), $payload);

        $response->assertRedirect(route('admin.welcome-content.edit'));

        $this->assertDatabaseHas('welcome_page_contents', [
            'key' => 'home',
            'hero_badge' => 'Badge Baru',
            'hero_title' => 'Judul Hero Baru',
            'footer_note' => 'Footer Note Baru',
        ]);
    }
}
