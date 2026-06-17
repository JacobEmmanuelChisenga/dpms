<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\Permit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAuditLogs');

        $subjectMap = [
            'users' => User::class,
            'drivers' => Driver::class,
            'permits' => Permit::class,
        ];

        $query = AuditLog::query()->with('actor')->orderByDesc('logged_at');

        if ($request->filled('event')) {
            $query->where('event', (string) $request->input('event'));
        }

        if ($request->filled('subject_filter')) {
            $class = $subjectMap[(string) $request->input('subject_filter')] ?? null;
            if ($class !== null) {
                $query->where('subject_type', $class);
            }
        }

        return view('audit_logs.index', [
            'logs' => $query->paginate(30)->withQueryString(),
            'subjectMap' => $subjectMap,
        ]);
    }
}
