<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_group()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/groups', [
            'name' => 'Test Group'
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('groups', ['name' => 'Test Group']);
        $this->assertDatabaseHas('roommates', [
            'user_id' => $user->id,
            'name' => $user->name
        ]);
    }

    public function test_user_can_join_group_with_valid_code()
    {
        $admin = User::factory()->create();
        $group = Group::create([
            'name' => 'Existing Group',
            'invite_code' => 'JOINME',
            'created_by' => $admin->id
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/groups/join', [
            'invite_code' => 'JOINME'
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($group->users->contains($user));
    }

    public function test_joining_with_invalid_code_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/groups/join', [
            'invite_code' => 'WRONG'
        ]);

        $response->assertSessionHasErrors('invite_code');
    }
}
