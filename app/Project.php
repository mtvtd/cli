<?php

namespace App;

class Project
{
    public static function path(): string
    {
        return getcwd();
    }

    public static function packageRoot(): string
    {
        return dirname(__DIR__);
    }
}
