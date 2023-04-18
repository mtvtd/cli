<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DeployCommand extends Command
{
    protected $signature = 'deploy';
    protected $description = 'Deploy an application.';

    public function handle(): int
    {
        // Read a config file from the root directory (or something).

        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
