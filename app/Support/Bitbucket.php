<?php

namespace Mtvtd\Deploy\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Bitbucket
{
    protected array $credentials = [
        'token' => null,
    ];

    protected function baseUri(): string
    {
        return 'https://api.bitbucket.org/2.0/';
    }

    public function setToken(string $token): self
    {
        $this->credentials['token'] = $token;

        return $this;
    }

    public function currentUser(): object
    {
        $uri = 'user';

        return Http::baseUrl($this->baseUri())
            ->withToken($this->credentials['token'])
            ->get($uri)
            ->throw()
            ->object();
    }

    public function defaultReviewers(string $repo): Collection
    {
        $uri = 'repositories/' . $repo . '/default-reviewers';

        return Http::baseUrl($this->baseUri())
            ->withToken($this->credentials['token'])
            ->get($uri)
            ->throw()
            ->collect('values');
    }

    public function pullRequests(string $repo): Collection
    {
        $uri = 'repositories/' . $repo . '/pullrequests';

        return Http::baseUrl($this->baseUri())
            ->withToken($this->credentials['token'])
            ->get($uri)
            ->throw()
            ->collect();
    }

    public function createPR(string $repository, string $source, string $destination, string $title, Collection $reviewers): object
    {
        $uri = 'repositories/' . $repository . '/pullrequests';

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
            ->withToken($this->credentials['token'])
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($uri, $data)
            ->throw()
            ->object();
    }

}
