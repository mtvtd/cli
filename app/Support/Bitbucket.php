<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Bitbucket
{
    protected function baseUri(): string
    {
        return 'https://api.bitbucket.org/2.0/';
    }

    public function currentUser(): object
    {
        $uri = 'user';

        return Http::baseUrl($this->baseUri())
            ->withBasicAuth(env('BITBUCKET_APP_USERNAME'), env('BITBUCKET_APP_PASSWORD'))
            ->get($uri)
            ->throw()
            ->object();
    }

    public function defaultReviewers(string $repo): Collection
    {
        $uri = 'repositories/' . $repo . '/default-reviewers';

        return Http::baseUrl($this->baseUri())
            ->withBasicAuth(env('BITBUCKET_APP_USERNAME'), env('BITBUCKET_APP_PASSWORD'))
            ->get($uri)
            ->throw()
            ->collect('values');
    }

    public function pullRequests(string $repo): Collection
    {
        $uri = 'repositories/' . $repo . '/pullrequests';

        return Http::baseUrl($this->baseUri())
            ->withBasicAuth(env('BITBUCKET_APP_USERNAME'), env('BITBUCKET_APP_PASSWORD'))
            ->get($uri)
            ->throw()
            ->collect();
    }

    public function createPR(string $repo, string $source, string $destination, string $title, Collection $reviewers): object
    {
        $uri = 'repositories/' . $repo . '/pullrequests';

        $data = [
            'title' => $title,
            'description' => 'Automatic PR',
            'source' => [
                'branch' => [
                    'name' => $source,
                ],
            ],
            'destination' => [
                'branch' => [
                    'name' => $destination,
                ],
            ],
            'close_source_branch' => true,
            'reviewers' => $reviewers->toArray(),
        ];

        return Http::baseUrl($this->baseUri())
            ->withBasicAuth(env('BITBUCKET_APP_USERNAME'), env('BITBUCKET_APP_PASSWORD'))
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($uri, $data)
            ->throw()
            ->object();
    }

}
