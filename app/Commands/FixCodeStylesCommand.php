<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class FixCodeStylesCommand extends Command
{
    protected $signature = 'fix-code-styles';
    protected $description = 'Fix Code Style';

    public function handle(): int
    {
        // Fix code styles with PHP-CS-Fixer

        $this->info('Fixing styles...');

        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
