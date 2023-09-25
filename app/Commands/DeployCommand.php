<?php

namespace App\Commands;

use App\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Process;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DeployCommand extends Command
{
    protected $signature = 'deploy';
    protected $description = 'Deploy an application.';

    protected array $outputLogs = [];

    public function handle(): int
    {
        $this->output->title('Deploy this application');

        $steps = Config::get('deploy.steps');
        $notificationEmails = Config::get('deploy.notification.failed');

        if (count($steps) === 0) {
            $this->output->error('Er zijn stappen ingesteld voor de deploy.');

            return self::FAILURE;
        }

        foreach ($steps as $key => $step) {
            $result = $this->task('Run step ' . $key, function () use ($step) {
                $process = new Process(explode(' ', $step));
                $process->run();

                $this->outputLogs[] = $process->isSuccessful() ? $process->getOutput() : $process->getErrorOutput();

                return $process->isSuccessful();
            });

            if ( ! $result) {
                 if(count($notificationEmails)) {
                     // Mail::send()
                 }

                return self::FAILURE;
            }
        }


        $this->output->success('All done.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
