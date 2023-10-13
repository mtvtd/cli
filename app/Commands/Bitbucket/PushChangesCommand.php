<?php

namespace Mtvtd\Deploy\Commands\Bitbucket;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class PushChangesCommand extends Command
{
    protected $signature = 'bitbucket:push-changes';
    protected $description = 'Push Changes to Bitbucket';

    public function handle(): int
    {


        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
