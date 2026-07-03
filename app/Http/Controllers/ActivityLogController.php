<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        if ($request->filled('action')) {
            $query->where('event', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('subject_type', 'like', '%' . $request->model . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = \App\Models\User::all();

        // Jika menggunakan Inertia.js untuk React:
        // return inertia('ActivityLogs/Index', ['logs' => $logs]);

        // Sementara return API JSON jika dipanggil manual oleh React (CSR)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($logs);
        }

        // Default Blade (jika masih digunakan)
        return view('activity_logs.index', compact('logs', 'users'));
    }
}
