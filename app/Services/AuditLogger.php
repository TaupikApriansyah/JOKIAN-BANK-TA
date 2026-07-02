<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditLogger
{
    public function log(
        Request $request,
        string $module,
        string $action,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        ?string $description = null,
    ): void {
        $user = $request->user();

        AuditLog::create([
            'user_id' => $user?->id,
            'role' => $user?->role?->value,
            'module' => $module,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : 'system',
            'subject_id' => $subject?->getKey(),
            'before_values' => $before,
            'after_values' => $after,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => $description,
        ]);
    }
}
