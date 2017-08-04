<?php

namespace ZFort\Skeleton\Test\Utils;

use Illuminate\Foundation\Exceptions\Handler;

class TestExceptionHandler extends Handler
{
    public function __construct()
    {
    }

    public function report(\Exception $e)
    {
    }

    public function render($request, \Exception $e)
    {
        throw $e;
    }
}
