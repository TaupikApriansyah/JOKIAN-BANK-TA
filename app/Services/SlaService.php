<?php

namespace App\Services;

use App\Enums\CaseStatus;
use App\Enums\SlaStatus;
use App\Enums\UserRole;
use App\Models\ServiceCase;
use App\Models\SlaNotification;
use App\Models\User;
use Carbon\Carbon;

class SlaService
{
    public function refresh(ServiceCase $serviceCase): ServiceCase
    {
        $serviceCase->loadMissing('serviceType');

        if (in_array($serviceCase->status, [CaseStatus::Selesai, CaseStatus::Ditolak], true)) {
            if ($serviceCase->sla_status !== SlaStatus::Selesai) {
                $serviceCase->update(['sla_status' => SlaStatus::Selesai]);
            }

            return $serviceCase->fresh();
        }

        $now = Carbon::now();
        $dueAt = $serviceCase->due_at;
        $totalMinutes = max(1, $serviceCase->received_at->diffInMinutes($dueAt));
        $remainingMinutes = $now->diffInMinutes($dueAt, false);
        $nearMinutes = max(
            (int) config('bank.sla.near_minutes', 60),
            (int) round($totalMinutes * ((int) config('bank.sla.near_percent', 20) / 100)),
        );

        $nextStatus = match (true) {
            $remainingMinutes < 0 => SlaStatus::Terlambat,
            $remainingMinutes <= $nearMinutes => SlaStatus::Mendekati,
            default => SlaStatus::Aman,
        };

        if ($serviceCase->sla_status !== $nextStatus) {
            $serviceCase->update(['sla_status' => $nextStatus]);

            if (in_array($nextStatus, [SlaStatus::Mendekati, SlaStatus::Terlambat], true)) {
                $this->createNotifications($serviceCase, $nextStatus);
            }
        }

        return $serviceCase->fresh();
    }

    public function refreshOpenCases(): void
    {
        ServiceCase::query()
            ->whereNotIn('status', [CaseStatus::Selesai->value, CaseStatus::Ditolak->value])
            ->orderBy('id')
            ->chunkById(100, fn ($cases) => $cases->each(fn (ServiceCase $case) => $this->refresh($case)));
    }

    private function createNotifications(ServiceCase $serviceCase, SlaStatus $status): void
    {
        $recipientIds = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($serviceCase): void {
                $query->where('id', $serviceCase->assigned_to)
                    ->orWhere('role', UserRole::Admin->value);
            })
            ->pluck('id');

        $title = $status === SlaStatus::Terlambat
            ? 'SLA berkas telah terlewati'
            : 'SLA berkas mendekati batas waktu';
        $message = $status === SlaStatus::Terlambat
            ? "Berkas {$serviceCase->file_number} telah melewati batas SLA. Mohon lakukan tindak lanjut."
            : "Berkas {$serviceCase->file_number} mendekati batas SLA pada {$serviceCase->due_at->format('d M Y H:i')}.";

        foreach ($recipientIds as $recipientId) {
            SlaNotification::query()->firstOrCreate(
                [
                    'service_case_id' => $serviceCase->id,
                    'recipient_id' => $recipientId,
                    'sla_status' => $status->value,
                ],
                [
                    'title' => $title,
                    'message' => $message,
                ],
            );
        }
    }
}
