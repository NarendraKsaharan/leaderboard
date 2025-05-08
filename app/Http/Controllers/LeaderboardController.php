<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\Log;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with('activities');

            $search = $request->search;
            $filter = $request->filter ?? null;
            
            $date = now();
            if ($search) {
                $query->where('id', $search);
            }

            if ($filter) {
                switch ($filter) {
                    case 'day':
                        $query->whereHas('activities', function($query) use ($date) {
                            $query->whereDate('performed_at', $date->toDateString());
                        });
                        break;
                    case 'month':
                        $query->whereHas('activities', function($query) use ($date) {
                            $query->whereMonth('performed_at', $date->month)
                                ->whereYear('performed_at', $date->year);
                        });
                        break;
                    case 'year':
                        $query->whereHas('activities', function($query) use ($date) {
                            $query->whereYear('performed_at', $date->year);
                        });
                        break;
                }
            }

            $users = $query->get()
                    ->map(function ($user) use ($filter, $date) {
                        switch ($filter) {
                            case 'day':
                                $user->total_points = $user->activities
                                    ->filter(function ($activity) use ($date) {
                                        return \Carbon\Carbon::parse($activity->performed_at)->toDateString() === $date->toDateString();
                                    })
                                    ->sum('points');
                                break;

                            case 'month':
                                $user->total_points = $user->activities
                                    ->filter(function ($activity) use ($date) {
                                        return \Carbon\Carbon::parse($activity->performed_at)->month === $date->month &&
                                            \Carbon\Carbon::parse($activity->performed_at)->year === $date->year;
                                    })
                                    ->sum('points');
                                break;

                            case 'year':
                                $user->total_points = $user->activities
                                    ->filter(function ($activity) use ($date) {
                                        return \Carbon\Carbon::parse($activity->performed_at)->year === $date->year;
                                    })
                                    ->sum('points');
                                break;

                            default:
                                $user->total_points = $user->activities->sum('points');
                                break;
                        }

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
                $user->rank = $rank;
                $prevPoints = $user->total_points;
            }

            return view('leaderboard', compact('users'));

        } catch (\Throwable $e) {
            \Log::error('Leaderboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong!');
        }
    }


    public function recalculate()
    {
        return redirect()->back()->with('success', 'Leaderboard updated successfully!');
    }
}