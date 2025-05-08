<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Leaderboard;

class LeaderboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::with('activities')->get()
            ->map(function ($user) {
                $user->total_points = $user->activities->sum('points');
                return $user;
            })
            ->sortByDesc('total_points')
            ->values();

        $prevPoints = null;
        $rank = 0;

        foreach ($users as $user) {
            if ($user->total_points !== $prevPoints) {
                $rank++;
            }

            Leaderboard::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_points' => $user->total_points,
                    'rank' => $rank
                ]
            );

            $prevPoints = $user->total_points;
        }
    }

}