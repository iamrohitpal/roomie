<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupPerformanceSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('phone', '9876543210')->first();
        if (! $user) {
            $this->command->error('User with phone 9876543210 not found.');

            return;
        }

        $count = 1000;
        $this->command->info("Creating $count groups for user: {$user->name}...");

        $batchSize = 100;
        for ($b = 0; $b < $count / $batchSize; $b++) {
            $groupsData = [];
            for ($i = 0; $i < $batchSize; $i++) {
                $groupsData[] = [
                    'name' => 'Scale Group '.($b * $batchSize + $i + 1),
                    'invite_code' => strtoupper(Str::random(6)),
                    'created_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('groups')->insert($groupsData);

            // Get IDs of inserted groups to attach user and create roommates
            $insertedGroupIds = DB::table('groups')
                ->where('created_by', $user->id)
                ->orderBy('id', 'desc')
                ->limit($batchSize)
                ->pluck('id');

            $groupUserData = [];
            $roommatesData = [];
            foreach ($insertedGroupIds as $groupId) {
                $groupUserData[] = [
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                    'role' => 'admin',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $roommatesData[] = [
                    'group_id' => $groupId,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('group_user')->insert($groupUserData);
            DB::table('roommates')->insert($roommatesData);

            $this->command->info('Batch '.($b + 1).' completed...');
        }

        $this->command->info("Successfully created $count groups!");
    }
}
