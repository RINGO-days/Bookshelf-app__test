<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadingPlan;
use App\Notifications\PlanReminderNotification;

class Check3daysAgo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-3days-ago';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期日の3日前に、ユーザーにその旨を通知する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('読書計画の期日の3日前の確認');

        ReadingPlan::with('user')
            ->where('target_date', now()->addDays(3)->format('Y-m-d'))
            ->where('status', '!=', 'expired')
            ->get()
            ->each(function ($remainderPlan) {
                $remainderPlan->user->notify(new PlanReminderNotification($remainderPlan,'3days-ago'));
            });

        $this->info('確認終了');
    }
}
