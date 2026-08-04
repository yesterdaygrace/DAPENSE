<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $logName = $request->query('log', 'default');
        $search = $request->query('search');

        $query = Activity::with('causer')
            ->where('log_name', $logName)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $activities = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Activity::where('log_name', $logName)->count(),
            'today' => Activity::where('log_name', $logName)
                ->whereDate('created_at', today())
                ->count(),
            'logins' => Activity::where('log_name', $logName)
                ->where('description', 'like', '%login%')
                ->count(),
            'creates' => Activity::where('log_name', $logName)
                ->where('event', 'created')
                ->count(),
        ];

        return view('modules.activity.index', compact('activities', 'stats', 'logName', 'search'));
    }
}
