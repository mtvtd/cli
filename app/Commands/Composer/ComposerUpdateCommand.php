<?php

namespace App\Commands\Composer;

use Symfony\Component\Process\Process;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComposerUpdateCommand extends Command
{
    protected $signature = 'composer:update';
    protected $description = 'Create composer-update branch';

    private ?string $branch = null;

    public function handle(): int
    {
        $this->output->title('Run Composer Update');

        $result = $this->task('Make sure we are on the master/main branch', function () {
            $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD']);
            $process->run();
            $this->branch = $process->isSuccessful() ? trim($process->getOutput()) : null;

            return in_array($this->branch, ['master', 'main'], true);
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Make sure we have a clean state', function () {
            $process = new Process(['git', 'status', '--porcelain']);
            $process->run();

            return $process->isSuccessful() && empty(trim($process->getOutput()));
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Pull latest changes', function () {
            // $process = new Process(['git', 'pull', 'origin', 'HEAD', '--ff-only']);
            $process = new Process(['git', 'pull', 'origin', 'HEAD']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Create Composer Update Branch', function () {
            $process = new Process(['git', 'checkout', '-B', 'hotfix/composer-update']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Run Composer Update', function () {
            $process = new Process(['composer', 'update', '-W', '--no-audit']);
            $process->run();

            return $process->isSuccessful();
        }, 'running ...');

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Add changes', function () {
            $process = new Process(['git', 'add', '.']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Commit changes', function () {
            $process = new Process(['git', 'commit', '-m', 'Composer Update']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Push changes to remote', function () {
            $process = new Process(['git', 'push', 'origin', 'HEAD', '-f']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Switch back to master/main branch', function () {
            $process = new Process(['git', 'checkout', $this->branch]);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Delete Composer Update Branch', function () {
            $process = new Process(['git', 'branch', '-D', 'hotfix/composer-update']);
            $process->run();

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $this->output->success('Composer Update ran successfully !');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
