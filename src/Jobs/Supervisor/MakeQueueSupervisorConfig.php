<?php

namespace Qruto\Formula\Jobs\Supervisor;

class MakeQueueSupervisorConfig extends MakeSupervisorConfig
{
    protected string $processName = 'queue';

    public function __construct(array $params = [], string $fileName = '', string $path = '/etc/supervisor/conf.d/')
    {
        $params = $params ?: [
            'command' => 'php artisan queue:work --sleep=3 --tries=3',
            'numprocs' => 3,
        ];

        parent::__construct($params, $fileName, $path);
    }
}
