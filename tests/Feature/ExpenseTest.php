<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Group;
use App\Models\Roommate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $group;
    protected $roommate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->group = Group::create([
            'name' => 'House',
            'invite_code' => 'TEST12',
            'created_by' => $this->user->id
        ]);
        $this->group->users()->attach($this->user->id, ['role' => 'admin']);
        
        $this->roommate = Roommate::create([
            'group_id' => $this->group->id,
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'phone' => $this->user->phone
        ]);

        $this->actingAs($this->user);
        session(['active_group_id' => $this->group->id]);
    }

    public function test_user_can_add_expense_with_valid_splits()
    {
        $response = $this->post('/expenses', [
            'description' => 'Pizza Party',
            'amount' => 500,
            'date' => now()->format('Y-m-d'),
            'splits' => [
                $this->roommate->id => 500
            ]
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('expenses', ['description' => 'Pizza Party', 'amount' => 500]);
    }

    public function test_expense_fails_if_splits_sum_does_not_match_total()
    {
        $response = $this->post('/expenses', [
            'description' => 'Lies',
            'amount' => 500,
            'date' => now()->format('Y-m-d'),
            'splits' => [
                $this->roommate->id => 400 // Sum is 400, but amount is 500
            ]
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseMissing('expenses', ['description' => 'Lies']);
    }

    public function test_expense_fails_with_invalid_data()
    {
        $response = $this->post('/expenses', [
            'description' => '',
            'amount' => -10,
            'date' => 'not-a-date',
            'splits' => []
        ]);

        $response->assertSessionHasErrors(['description', 'amount', 'date', 'splits']);
    }
}
