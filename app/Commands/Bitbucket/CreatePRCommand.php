<?php

namespace App\Commands\Bitbucket;

use App\Facades\Config;
use App\Facades\Bitbucket;
use Illuminate\Support\Str;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CreatePRCommand extends Command
{
    protected $signature = 'bitbucket:create-pr {source} {destination}';
    protected $description = 'Create a PR on Bitbucket.';

    public function handle(): int
    {
        $this->output->title('Create a PR on Bitbucket');

        // Variabelen
        $repository = Config::get('bitbucket.repository');
        $source = $this->argument('source');
        $destination = $this->argument('destination');
        $title = Config::get('bitbucket.commit.title', '[Automated] - Composer Update - {date}');
        $title = Str::of($title)->replace('{date}', now()->format('d-m-Y H:i:s'))->toString();

        // Set Token
        Bitbucket::setToken(Config::get('bitbucket.token'));

        // Which Reviewers
        $this->info('- Get default reviewers');
        $defaultReviewers = Bitbucket::defaultReviewers($repository);
        $prReviewers = $defaultReviewers->pluck('uuid')
            ->map(fn(string $uuid) => ['uuid' => $uuid])
            ->values();

        // Create the PR
        $this->info('- Create PR');
        Bitbucket::createPR(
            repository: $repository,
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
