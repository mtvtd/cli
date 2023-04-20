<?php

namespace App\Support;

use App\Project;
use Illuminate\Support\Arr;

class MTVTDConfig
{
    public function __construct(protected $config = [])
    {
        //
    }

    public static function loadLocal(): array
    {
        if (file_exists(Project::path() . '/mtvtd.json')) {
            return tap(json_decode(file_get_contents(Project::path() . '/mtvtd.json'), true, 512, JSON_THROW_ON_ERROR), function ($configuration) {
                if (! is_array($configuration)) {
                    abort(1, 'The configuration file mtvtd.json is not valid JSON.');
                }
            });
        }

        return [];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }
}
