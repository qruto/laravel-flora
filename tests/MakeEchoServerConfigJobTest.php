<?php

namespace MadWeb\Initializer\Test;

use MadWeb\Initializer\Jobs\MakeEchoServerConfig;
use MadWeb\Initializer\Run;

class MakeEchoServerConfigJobTest extends RunnerCommandsTestCase
{
    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function dispatch_job($command)
    {
        $this->declareCommands(function (Run $run) {
            $run->dispatch(new MakeEchoServerConfig);
        }, $command);

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

    /**
     * @test
     * @dataProvider initCommandsSet
     */
    public function override_default_configuration($command)
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
        }, $command);

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
