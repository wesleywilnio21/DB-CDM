<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('model_type', $request->model);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $logs  = $query->paginate(20)->withQueryString();
        $users = User::all();

        return view('activity_logs.index', compact('logs', 'users'));
    }
}
