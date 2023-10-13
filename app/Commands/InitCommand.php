<?php

namespace Mtvtd\Deploy\Commands;

use Mtvtd\Deploy\Project;
use Mtvtd\Deploy\Support\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class InitCommand extends Command
{
    protected $signature = 'init {--force}';
    protected $description = 'Init MTVTD Deploy.';

    public function handle(): int
    {
        $this->task('Copy example Config', function () {
            if ( ! $this->option('force') && File::exists(Project::path() . '/' . Config::SETTING_FILE)) {
                return false;
            }

            return File::copy(
                Project::packageRoot() . '/' . Config::PACKAGE_FILE,
                Project::path() . '/' . Config::SETTING_FILE
            );
        });

        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
