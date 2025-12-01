<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Hold;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // Release expired holds every minute
        $schedule->call(function () {
            Hold::where('expires_at', '<=', now())
                ->where('released', false)
                ->chunkById(100, function ($holds) {
                    foreach ($holds as $h) {
                        try {
                            $h->release();
                        } catch (\Throwable $e) {
                            Log::error('hold-release-error: '.$e->getMessage());
                        }
                    }
                });
        })->everyMinute();
    }
}
