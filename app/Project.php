<?php

namespace App;

class Project
{
    public static function path(): string
    {
        return getcwd();
    }
}
