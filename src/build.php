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

App::install('local', fn (Runner $run) => $run
    ->command('key:generate')
    ->command('migrate')
    ->command('storage:link')
    ->exec('npm install')
    ->exec('npm run build')
);

App::install('production', fn (Runner $run) => $run
    ->command('key:generate', ['--force' => true])
    ->command('migrate', ['--force' => true])
    ->command('storage:link')
    ->command('route:cache')
    ->command('config:cache')
    ->command('event:cache')
);

App::update('local', fn (Runner $run) => $run
    ->exec('npm install')
    ->exec('npm run build')
    ->command('migrate')
    ->command('cache:clear')
);

App::update('production', fn (Runner $run) => $run
    ->command('route:cache')
    ->command('config:cache')
    ->command('event:cache')
    ->command('migrate', ['--force' => true])
    ->command('cache:clear')
    ->command('queue:restart')
);
