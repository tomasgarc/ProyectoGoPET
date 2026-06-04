<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clean up analytics file before each test
        $analyticsPath = storage_path('app/analytics.json');
        if (File::exists($analyticsPath)) {
            File::delete($analyticsPath);
        }
    }

    protected function tearDown(): void
    {
        // Clean up analytics file after each test
        $analyticsPath = storage_path('app/analytics.json');
        if (File::exists($analyticsPath)) {
            File::delete($analyticsPath);
        }

        parent::tearDown();
    }

    /**
     * Test that dashboard page is displayed for authenticated and verified users.
     */
    public function test_dashboard_page_is_displayed(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that guest users are redirected to login.
     */
    public function test_guests_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test that the update-analytics endpoint runs and updates the analytics file for admin users.
     */
    public function test_update_analytics_updates_json_file(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $analyticsPath = storage_path('app/analytics.json');
        $this->assertFalse(File::exists($analyticsPath));

        $response = $this
            ->actingAs($user)
            ->post('/dashboard/update-analytics');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(File::exists($analyticsPath));

        $stats = json_decode(File::get($analyticsPath), true);
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('total_dogs', $stats);
        $this->assertArrayHasKey('total_requests', $stats);
    }

    /**
     * Test that non-admin users cannot update analytics.
     */
    public function test_non_admin_cannot_update_analytics(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'role' => 'user',
        ]);

        $response = $this
            ->actingAs($user)
            ->post('/dashboard/update-analytics');

        $response->assertStatus(403);
    }
}
