<?php

namespace App\Support;

use App\Project;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Config
{
    public const SETTING_FILE = "mtvtd.json";
    public const PACKAGE_FILE = "resources/mtvtd-config.json";

    public function __construct(protected $config = [])
    {
        //
    }

    public static function loadLocal(): array
    {
        if (file_exists(Project::path() . '/' . self::SETTING_FILE)) {
            return tap(json_decode(file_get_contents(Project::path() . '/' . self::SETTING_FILE), true, 512, JSON_THROW_ON_ERROR), function ($configuration) {
                if ( ! is_array($configuration)) {
                    abort(1, 'The configuration file mtvtd.json is not valid JSON.');
                }
            });
        }

        if (file_exists(Project::packageRoot() . '/' . self::PACKAGE_FILE)) {
            return tap(json_decode(file_get_contents(Project::packageRoot() . '/' . self::PACKAGE_FILE), true, 512, JSON_THROW_ON_ERROR), function ($configuration) {
                if ( ! is_array($configuration)) {
                    abort(1, 'The configuration file mtvtd-config.json is not valid JSON.');
                }
            });
        }

        return [];
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $value = Arr::get($this->config, $key);
        if (Str::of($value)->startsWith('$')) {
            $value = env(substr($value, 1));
        }

        return $value ?? $default;
    }
}
