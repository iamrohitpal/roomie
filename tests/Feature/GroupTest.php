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
            'name' => 'Test Group',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('groups', ['name' => 'Test Group']);
        $this->assertDatabaseHas('roommates', [
            'user_id' => $user->id,
            'name' => $user->name,
        ]);
    }

    public function test_user_can_join_group_with_valid_code()
    {
        $admin = User::factory()->create();
        $group = Group::create([
            'name' => 'Existing Group',
            'invite_code' => 'JOINME',
            'created_by' => $admin->id,
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/groups/join', [
            'invite_code' => 'JOINME',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertTrue($group->users->contains($user));
    }

    public function test_joining_with_invalid_code_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/groups/join', [
            'invite_code' => 'WRONG',
        ]);

        $response->assertSessionHasErrors('invite_code');
    }

    public function test_user_can_search_groups()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Group::create(['name' => 'Vacation Group', 'invite_code' => 'VAC001', 'created_by' => $user->id])->users()->attach($user->id, ['role' => 'admin']);
        Group::create(['name' => 'Work Group', 'invite_code' => 'WRK002', 'created_by' => $user->id])->users()->attach($user->id, ['role' => 'admin']);

        $response = $this->get(route('groups.index', ['search' => 'Vacation']));

        $response->assertStatus(200);
        $response->assertSee('Vacation Group');
        $response->assertDontSee('Work Group');
    }

    public function test_group_list_returns_paginated_json_via_ajax()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create 25 groups
        for ($i = 0; $i < 25; $i++) {
            $group = Group::create(['name' => 'Group '.$i, 'invite_code' => 'GRP'.$i, 'created_by' => $user->id]);
            $group->users()->attach($user->id, ['role' => 'member']);
        }

        $response = $this->get(route('groups.index'), [
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'next_page']);
        $this->assertNotNull($response->json('next_page'));
    }
}
