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

            if ($request->has('search') && $request->search) {
                $query->where('id', $request->search);
            }

            if ($request->has('filter') && $request->filter && !empty($request->filter)) {
                $filter = $request->filter;
                $date = now();
                switch ($filter) {
                    case 'day':
                        $query->whereHas('activities', function($query) use ($date) {
                            $query->whereRaw('DATE(performed_at) = ?', [$date->toDateString()]);
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
                    default:
                        break;
                }
            }

            $users = $query->get()
                ->map(function ($user) use ($filter, $date) {
                    if ($filter == 'day') {
                        $user->total_points = $user->activities
                            ->filter(function ($activity) use ($date) {
                                $performedAt = \Carbon\Carbon::parse($activity->performed_at);
                                return $performedAt->toDateString() === $date->toDateString();
                            })
                            ->sum('points');
                    } else {
                        $user->total_points = $user->activities->sum('points');
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