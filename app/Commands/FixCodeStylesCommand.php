<?php

namespace App\Commands;

use App\Support\PhpCsFixer;
use PhpCsFixer\Console\Output\ErrorOutput;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;

class FixCodeStylesCommand extends Command
{
    protected $signature = 'fix-code-styles {--dry-run}';
    protected $description = 'Fix Code Style';

    protected array $response = [];

    public function handle(): int
    {
        $this->output->title('Fix code styles.');

        $this->task('Run PHP-CS-Fixer', function () {
            $phpcsfixer = new PhpCsFixer;

            if ( ! $this->option('dry-run')) {
                $this->response = $phpcsfixer->fix();
            } else {
                $this->response = $phpcsfixer->lint();
            }

            return true;
        });

        $exitcode =  (new FixCommandExitStatusCalculator)->calculate(
            $this->option('dry-run'),
            count($this->response['changes'] ?? []) > 0,
            count($this->response['invalidErrors'] ?? []) > 0,
            count($this->response['exceptionErrors'] ?? []) > 0,
            count($this->response['lintErrors'] ?? []) > 0
        );

        if ($exitcode === 0) {
            $this->output->success('All done.');
        } else {
            $this->output->error('Er is iets mis gegaan...');
        }

        $errorOutput = new ErrorOutput($this->output);

        if (count($this->response['invalidErrors']) > 0) {
            $errorOutput->listErrors('linting before fixing', $this->response['invalidErrors']);
        }

        if (count($this->response['exceptionErrors']) > 0) {
            $errorOutput->listErrors('fixing', $this->response['exceptionErrors']);
        }

        if (count($this->response['lintErrors']) > 0) {
            $errorOutput->listErrors('linting after fixing', $this->response['lintErrors']);
        }

        return $exitcode;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
