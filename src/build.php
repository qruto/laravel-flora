<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\App;
use Qruto\Initializer\Contracts\Runner;
use Qruto\Initializer\Run;

App::install('local', fn (Runner $run) => $run
    ->command('key:generate')
    ->command('migrate')
    ->command('storage:link')
    ->instruction('build')
);

App::install('production', fn (Runner $run) => $run
    ->command('key:generate', ['--force' => true])
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
    ->instruction('cache')
    ->instruction('build')
);

App::update('local', fn (Run $run) => $run
    ->command('migrate')
    ->command('cache:clear')
    ->instruction('build')
);

App::update('production', fn (Runner $run) => $run
    ->instruction('cache')
    ->command('migrate', ['--force' => true])
    ->command('cache:clear')
    ->command('queue:restart')
    ->instruction('build')
);
