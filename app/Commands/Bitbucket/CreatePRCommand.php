<?php

namespace App\Commands\Bitbucket;

use App\Facades\Bitbucket;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreatePRCommand extends Command
{
    protected $signature = 'bitbucket:create-pr {source} {destination}';
    protected $description = 'Create a PR on Bitbucket.';

    public function handle(): int
    {
        $this->output->title('Create a PR on Bitbucket');

        $repo = env('BITBUCKET_REPO');

        // Get Current User
        $user = Bitbucket::currentUser();
        $uuid = $user->uuid;

        // Which Reviewers
        $defaultReviewers = Bitbucket::defaultReviewers($repo);
        $prReviewers = $defaultReviewers->pluck('uuid')
            ->filter(fn(string $value) => $value !== $uuid)
            ->map(fn(string $uuid) => ['uuid' => $uuid])
            ->values();

        // Variabelen
        $source = $this->argument('source');
        $destination = $this->argument('destination');
        $title = '[CircleCI] - Composer Update - ' . now()->format('d-m-Y H:i:s');

        // Create the PR
        Bitbucket::createPR(
            repo: $repo,
            source: $source,
            destination: $destination,
            title: $title,
            reviewers: $prReviewers
        );

        $this->output->success('PR Created.');

        return self::SUCCESS;
    }

    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
