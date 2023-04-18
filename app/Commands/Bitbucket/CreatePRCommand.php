<?php

namespace App\Commands\Bitbucket;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreatePRCommand extends Command
{
    protected $signature = 'bitbucket:create-pr';
    protected $description = 'Create a PR on Bitbucket.';

    public function handle(): int
    {
        // Create a PR on Bitbucket.

        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
