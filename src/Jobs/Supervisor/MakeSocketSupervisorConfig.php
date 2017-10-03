<?php

namespace MadWeb\Initializer\Jobs\Supervisor;

class MakeSocketSupervisorConfig extends MakeSupervisorConfig
{
    protected $processName = 'socket';

    public function __construct(array $params = [], string $fileName = '', string $path = '/etc/supervisor/conf.d/')
    {
        $params = $params ?: [
            'command' => 'node ./node_modules/.bin/laravel-echo-server start',
        ];

        parent::__construct($params, $fileName, $path);
    }
}
