<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoCheckoutAttendance extends Command
{
    /**
     * php artisan attendance:auto-checkout
     */
    protected $signature = 'attendance:auto-checkout';

    protected $description = 'Auto checkout any employee who forgot to checkout — checkout time = checkin time + fixed work hours';

    // Standard work duration per day (in hours)
    private const WORK_HOURS = 9;

    public function handle(): int
    {
        $this->info('Starting auto-checkout job at ' . now()->toDateTimeString());

        // Only target sessions that are still active and were checked in TODAY
        // (adjust the whereDate() if you also want to sweep up stale sessions from earlier days)
        $activeLogs = AttendanceLog::where('status', 'active')
            ->whereDate('checkin_at', today())
            ->get();

        if ($activeLogs->isEmpty()) {
            $this->info('No pending check-ins found. Nothing to do.');
            return self::SUCCESS;
        }

        $processed = 0;
        $failed    = 0;

        foreach ($activeLogs as $log) {
            DB::beginTransaction();

            try {
                // Checkout timestamp = checkin time + 9 hours
                $checkoutAt = Carbon::parse($log->checkin_at)->addHours(self::WORK_HOURS);

                // Safety: if checkin + 9hrs lands in the future (e.g. employee
                // checked in late in the evening), cap it at "now" so we never
                // insert a checkout timestamp that hasn't happened yet.
                if ($checkoutAt->greaterThan(now())) {
                    $checkoutAt = now();
                }

                $sessionDuration = (int) $checkoutAt->diffInSeconds($log->checkin_at);

                $log->update([
                    'checkout_latitude'  => $log->checkin_latitude,   // no GPS available, reuse check-in location
                    'checkout_longitude' => $log->checkin_longitude,
                    'checkout_address'   => 'Auto checkout (missed manual checkout)',
                    'checkout_accuracy'  => null,
                    'checkout_at'        => $checkoutAt,
                    'session_duration'   => $sessionDuration,
                    'checkout_status'    => $log->checkin_status, // mirror check-in status since no real GPS ping
                    'status'             => 'completed',
                    'is_auto_checkout'   => true,
                ]);

                DB::commit();
                $processed++;

                $this->line("✔ Auto-checked out log #{$log->id} ({$log->name}) — checkin: {$log->checkin_at->toTimeString()} → checkout: {$checkoutAt->toTimeString()}");
            } catch (\Throwable $e) {
                DB::rollBack();
                $failed++;

                Log::error('Auto-checkout failed for attendance log', [
                    'log_id' => $log->id,
                    'error'  => $e->getMessage(),
                ]);

                $this->error("✘ Failed for log #{$log->id}: {$e->getMessage()}");
            }
        }

        $this->info("Auto-checkout complete. Processed: {$processed}, Failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}