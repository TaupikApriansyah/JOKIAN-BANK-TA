<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()->with('user')->latest();

        if ($module = $request->string('module')->trim()->value()) {
            $query->where('module', $module);
        }

        if ($action = $request->string('action')->trim()->value()) {
            $query->where('action', $action);
        }

        return view('admin.audit.index', ['logs' => $query->paginate(25)->withQueryString()]);
    }
}
