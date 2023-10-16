<?php

namespace Mtvtd\Deploy\Commands\Composer;

use Mtvtd\Deploy\Facades\Config;
use Symfony\Component\Process\Process;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ComposerUpdateCommand extends Command
{
    protected $signature = 'composer:update';
    protected $description = 'Create composer-update branch';

    private ?string $mainBranch = null;

    public function handle(): int
    {
        $this->output->title('Run Composer Update');

        $result = $this->task('Make sure we are on the master/main branch', function () {
            $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD']);
            $process->run();
            $this->mainBranch = $process->isSuccessful() ? trim($process->getOutput()) : null;

            return in_array($this->mainBranch, ['master', 'main'], true);
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        if ($gitUser = Config::get('git.user')) {
            $result = $this->task('Set Git User', function () use ($gitUser) {
                $process = new Process(['git', 'config', 'user.email', $gitUser]);
                $process->run();

                if ( ! $process->isSuccessful()) {
                    throw new \Exception($process->getErrorOutput());
                }

                return $process->isSuccessful();
            });

            if ( ! $result) {
                return self::FAILURE;
            }
        }

        $result = $this->task('Make sure we have a clean state', function () {
            $process = new Process(['git', 'status', '--porcelain']);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful() && empty(trim($process->getOutput()));
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Pull latest changes', function () {
            $process = Config::get('git.pull.ff-only')
                ? new Process(['git', 'pull', 'origin', 'HEAD', '--ff-only'])
                : new Process(['git', 'pull', 'origin', 'HEAD']);

            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $updateBranch = Config::get('composer.update-branch') ?? 'composer-update';

        $result = $this->task('Create Composer Update Branch', function () use ($updateBranch) {
            $process = new Process(['git', 'checkout', '-B', $updateBranch]);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Run Composer Update', function () {
            $process = new Process(['composer', 'update', '-W', '--no-audit']);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        }, 'running ...');

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Add changes', function () {
            $process = new Process(['git', 'add', '.']);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Commit changes', function () {
            $process = new Process(['git', 'commit', '-m', 'Composer Update']);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Push changes to remote', function () {
            $process = new Process(['git', 'push', 'origin', 'HEAD', '-f']);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Switch back to master/main branch', function () {
            $process = new Process(['git', 'checkout', $this->mainBranch]);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

            return $process->isSuccessful();
        });

        if ( ! $result) {
            return self::FAILURE;
        }

        $result = $this->task('Delete Composer Update Branch', function () use ($updateBranch) {
            $process = new Process(['git', 'branch', '-D', $updateBranch]);
            $process->run();

            if ( ! $process->isSuccessful()) {
                throw new \Exception($process->getErrorOutput());
            }

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
