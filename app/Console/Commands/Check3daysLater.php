<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadingPlan;
use App\Notifications\PlanReminderNotification;

class Check3daysLater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-3days-later';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期日の3日後に、ユーザーにその旨を通知する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('読書計画の期日の3日後の確認');

        ReadingPlan::with('user')
            ->where('target_date', now()->subDays(3)->format('Y-m-d'))
            ->where('status', '!=', 'expired')
            ->get()
            ->each(function ($remainderPlan) {
                $remainderPlan->user->notify(new PlanReminderNotification($remainderPlan,'3days-later'));
            });

        $this->info('確認終了');
    }
}
