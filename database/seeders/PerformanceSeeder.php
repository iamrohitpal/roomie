<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Roommate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceSeeder extends Seeder
{
    public function run()
    {
        // Use the group with user's number 9876543210 if possible
        $user = User::where('phone', '9876543210')->first();
        $group = null;

        if ($user) {
            $group = $user->groups()->first();
        }

        if (! $group) {
            $group = Group::first() ?? Group::create(['name' => 'Performance Test Group', 'invite_code' => 'PERF10']);
        }

        $roommates = Roommate::where('group_id', $group->id)->get();
        if ($roommates->count() < 2) {
            for ($i = 0; $i < 5; $i++) {
                Roommate::create([
                    'group_id' => $group->id,
                    'name' => 'Roommate '.($i + 1),
                    'phone' => '900000000'.$i,
                ]);
            }
            $roommates = Roommate::where('group_id', $group->id)->get();
        }

        $count = 10000;
        $this->command->info("Seeding $count expenses for group: {$group->name} (ID: {$group->id})");

        $batchSize = 1000;
        $categories = ['Groceries', 'Bills', 'Food', 'Entertainment', 'Other'];
        $descriptions = ['Weekly Groceries', 'Monthly Rent', 'Electricity Bill', 'Team Dinner', 'Internet Package', 'Water Bill', 'Maintenance', 'Gas Refill'];

        for ($b = 0; $b < $count / $batchSize; $b++) {
            $expensesData = [];
            for ($i = 0; $i < $batchSize; $i++) {
                $expensesData[] = [
                    'group_id' => $group->id,
                    'payer_id' => $roommates->random()->id,
                    'description' => $descriptions[array_rand($descriptions)].' '.($b * $batchSize + $i + 1),
                    'amount' => rand(100, 5000),
                    'date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                    'category' => $categories[array_rand($categories)],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('expenses')->insert($expensesData);

            // Get IDs of inserted expenses for splits
            $insertedIds = DB::table('expenses')
                ->where('group_id', $group->id)
                ->orderBy('id', 'desc')
                ->limit($batchSize)
                ->pluck('id', 'amount'); // Amount as key to calculate split

            $splitsData = [];
            foreach ($insertedIds as $amount => $id) {
                $splitRoommates = $roommates->random(rand(2, count($roommates)));
                $splitAmount = $amount / count($splitRoommates);
                foreach ($splitRoommates as $sr) {
                    $splitsData[] = [
                        'expense_id' => $id,
                        'roommate_id' => $sr->id,
                        'amount' => $splitAmount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            DB::table('expense_splits')->insert($splitsData);

            $this->command->info('Batch '.($b + 1).' completed...');
        }

        $this->command->info('Seeding completed!');
    }
}
