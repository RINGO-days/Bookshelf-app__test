<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReadingPlan;
use App\Notifications\CheckExpiredNotification;

class CheckExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '期日を過ぎた読書計画のステータスを「期日超え」にし、ユーザーに通知する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('読書計画の期日の超過の確認');

        ReadingPlan::with('user')
            ->where('target_date', '<', now())
            ->where('status', '!=', 'expired')
            ->get()
            ->map(function ($remainderPlan) {
                $remainderPlan->update([
                    'status' => 'expired'
                ]);
                $remainderPlan->user->notify(new CheckExpiredNotification($remainderPlan));
            });

        $this->info('確認終了');
    }
}
