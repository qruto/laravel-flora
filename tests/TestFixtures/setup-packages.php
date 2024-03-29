<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

/*
|--------------------------------------------------------------------------
| Build Instructions
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\App;
use Qruto\Flora\Run;

App::install('local', fn (Run $run) => $run
    ->command('key:generate')
    ->command('migrate')
    ->command('storage:link')
    ->script('build')
    ->command('ide-helper:generate')
    ->command('ide-helper:meta')
    ->command('ide-helper:models', ['--nowrite' => true])
    ->command('ide-helper:eloquent')
);

App::install('production', fn (Run $run) => $run
    ->command('key:generate', ['--force' => true])
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
    ->script('cache')
    ->script('build')
);

App::update('local', fn (Run $run) => $run
    ->command('migrate')
    ->command('cache:clear')
    ->script('build')
    ->command('ide-helper:generate')
    ->command('ide-helper:meta')
    ->command('ide-helper:models', ['--nowrite' => true])
    ->command('ide-helper:eloquent')
);

App::update('production', fn (Run $run) => $run
    ->script('cache')
    ->command('migrate', ['--force' => true])
    ->command('cache:clear')
    ->command('horizon:terminate')
    ->script('build')
);
