<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $remainedPlans = ReadingPlan::where('target_date', '<', now())
                ->where('status', '!=', 'expired')
                ->get();

            $remainedPlans->map(function ($plan) {
                $plan->update([
                    'status' => 'experid'
                ]);

                $plan->user->notify(new PlanRemainderNotification($plan));
            });
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
