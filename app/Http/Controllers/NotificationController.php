<?php

namespace App\Http\Controllers;

use App\Models\SlaNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('notifications.index', [
            'notifications' => SlaNotification::query()
                ->where('recipient_id', $request->user()->id)
                ->with('serviceCase.customer')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function markAsRead(Request $request, SlaNotification $notification, AuditLogger $audit): RedirectResponse
    {
        abort_unless($notification->recipient_id === $request->user()->id, 403);

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
            $audit->log($request, 'sla_notification', 'mark_read', $notification, ['read_at' => null], ['read_at' => now()->toDateTimeString()], 'Notifikasi SLA dibaca.');
        }

        return redirect()->route('cases.show', $notification->serviceCase);
    }
}
