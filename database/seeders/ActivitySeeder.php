<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Faker\Factory as Faker;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        User::all()->each(function($user) use ($faker) {
            for ($i = 0; $i < 5; $i++) {
                $dateType = rand(1, 3);

                switch ($dateType) {
                    case 1:
                        $activityDate = now();
                        break;
                    case 2:
                        $activityDate = $faker->dateTimeThisMonth();
                        break;
                    case 3:
                        $activityDate = $faker->dateTimeThisYear();
                        break;
                    default:
                        $activityDate = now();
                }

                $user->activities()->create([
                    'performed_at' => $activityDate,
                    'points' => 20,
                ]);
            }
        });
    }
}