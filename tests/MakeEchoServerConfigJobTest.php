<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Run;
use MadWeb\Initializer\Jobs\MakeEchoServerConfig;

class MakeEchoServerConfigJobTest extends RunnerCommandsTestCase
{
    /** @test */
    public function dispatch_job()
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeEchoServerConfig);
        });

        $config_path = base_path('laravel-echo-server.json');

        $this->assertJsonStringEqualsJsonFile($config_path, json_encode([
            'authHost' => url('/'),
            'authEndpoint' => '/broadcasting/auth',
            'database' => 'redis',
            'databaseConfig' => [
                'redis' => [
                    'host' => config('database.redis.default.host'),
                    'port' => config('database.redis.default.port'),
                ],
                'sqlite' => [
                    'databasePath' => '/storage/laravel-echo-server.sqlite',
                ],
            ],
            'devMode' => config('app.debug'),
            'host' => parse_url(url('/'), PHP_URL_HOST),
            'port' => 6001,
            'protocol' => 'http',
            'socketio' => [],
            'sslCertPath' => '',
            'sslKeyPath' => '',
            'sslCertChainPath' => '',
            'sslPassphrase' => '',
            'apiOriginAllow' => [
                'allowCors' => false,
                'allowOrigin' => '',
                'allowMethods' => '',
                'allowHeaders' => '',
            ],
        ]));

        unlink($config_path);
    }

    /** @test */
    public function override_default_configuration()
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeEchoServerConfig([
                'authHost' => 'http://some-awesome-host.com',
                'databaseConfig' => [
                    'redis' => [
                        'host' => 'some-another-host',
                    ],
                ],
                'port' => 1234,
            ]));
        });

        $config_path = base_path('laravel-echo-server.json');

        $this->assertJsonStringEqualsJsonFile($config_path, json_encode([
            'authHost' => 'http://some-awesome-host.com',
            'authEndpoint' => '/broadcasting/auth',
            'database' => 'redis',
            'databaseConfig' => [
                'redis' => [
                    'host' => 'some-another-host',
                    'port' => config('database.redis.default.port'),
                ],
                'sqlite' => [
                    'databasePath' => '/storage/laravel-echo-server.sqlite',
                ],
            ],
            'devMode' => config('app.debug'),
            'host' => parse_url(url('/'), PHP_URL_HOST),
            'port' => 1234,
            'protocol' => 'http',
            'socketio' => [],
            'sslCertPath' => '',
            'sslKeyPath' => '',
            'sslCertChainPath' => '',
            'sslPassphrase' => '',
            'apiOriginAllow' => [
                'allowCors' => false,
                'allowOrigin' => '',
                'allowMethods' => '',
                'allowHeaders' => '',
            ],
        ]));

        unlink($config_path);
    }
}
