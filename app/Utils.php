<?php


namespace App;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Utils
{

    public static function genUuid()
    {
        return (string) Str::orderedUuid();
    }

}
