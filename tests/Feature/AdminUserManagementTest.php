<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Dog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Non-admin users should be unauthorized to access user administration.
     */
    public function test_non_admin_cannot_access_user_management(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->get(route('admin.users.show', $otherUser));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('admin.users.toggle-ban', $otherUser));
        $response->assertStatus(403);
    }

    /**
     * Admin can view user management page, search users and view details with their dogs.
     */
    public function test_admin_can_manage_users_and_view_dogs(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => 'John Doe', 'role' => 'user']);
        
        $dog1 = Dog::create([
            'user_id' => $user->id,
            'name' => 'Fido',
            'breed' => 'Golden Retriever',
            'age' => 3,
            'size' => 'grande',
            'sex' => 'macho',
        ]);

        // 1. Check Index List
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('John Doe');

        // 2. Check Search functionality
        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'John']));
        $response->assertOk();
        $response->assertSee('John Doe');

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => 'NonExistent']));
        $response->assertOk();
        $response->assertDontSee('John Doe');

        // 3. Check Details and Dogs
        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));
        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('Fido');
        $response->assertSee('Golden Retriever');
    }

    /**
     * Admin can ban and unban users.
     */
    public function test_admin_can_ban_and_unban_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertNull($user->banned_at);

        // Ban
        $response = $this->actingAs($admin)->post(route('admin.users.toggle-ban', $user));
        $response->assertRedirect();
        $user->refresh();
        $this->assertNotNull($user->banned_at);
        $this->assertTrue($user->isBanned());

        // Unban
        $response = $this->actingAs($admin)->post(route('admin.users.toggle-ban', $user));
        $response->assertRedirect();
        $user->refresh();
        $this->assertNull($user->banned_at);
        $this->assertFalse($user->isBanned());
    }

    /**
     * Banned user is forced logged out and restricted from accessing auth pages.
     */
    public function test_banned_user_is_logged_out_and_restricted(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'banned_at' => now(),
        ]);

        // Access dashboard
        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Tu cuenta ha sido suspendida por un administrador.');
        
        $this->assertGuest();
    }

    /**
     * Admin or any user can start a direct chat with another user.
     */
    public function test_user_can_start_direct_chat_with_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Authenticated admin starts a direct chat with user
        $response = $this->actingAs($admin)->post(route('chats.start-direct', $user));
        
        $this->assertDatabaseHas('chats', [
            'care_request_id' => null,
            'user_id' => $user->id,
            'creator_id' => $admin->id,
        ]);

        $chat = \App\Models\Chat::whereNull('care_request_id')
            ->where('user_id', $user->id)
            ->where('creator_id', $admin->id)
            ->first();

        $this->assertNotNull($chat);
        $response->assertRedirect(route('chats.index', ['chat' => $chat->id]));
    }

    /**
     * Direct chat button is visible in admin user management and user detail view for other users.
     */
    public function test_admin_user_views_contain_chat_buttons(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        // Check index view
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee(route('chats.start-direct', $user));

        // Check show view
        $response = $this->actingAs($admin)->get(route('admin.users.show', $user));
        $response->assertOk();
        $response->assertSee(route('chats.start-direct', $user));
        
        // Admin should not see chat button for themselves
        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertDontSee(route('chats.start-direct', $admin));
    }
}
