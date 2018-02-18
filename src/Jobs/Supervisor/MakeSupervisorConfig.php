<?php

namespace MadWeb\Initializer\Jobs\Supervisor;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Foundation\Bus\Dispatchable;

abstract class MakeSupervisorConfig
{
    use Dispatchable, Queueable;

    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Supervisor config folder path.
     */
    protected $path;

    /**
     * Supervisor configuration name.
     */
    protected $fileName;

    /**
     * Supervisor config parameters.
     */
    protected $params;

    /**
     * Name of the supervisor process.
     */
    protected $processName = '';

    /**
     * Create a new job instance.
     */
    public function __construct(array $params = [], string $fileName = '', string $path = '/etc/supervisor/conf.d/')
    {
        $this->path = $path;
        $this->fileName = $fileName ?: $this->configName().'.conf';
        $this->params = $params;
        $this->container = Container::getInstance();
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle()
    {
        file_put_contents(
            $this->path.$this->fileName,
            $this->makeSupervisorConfig($this->processName, $this->params)
        );

        return 'Supervisor config file created. Path: '.$this->path.$this->fileName;
    }

    protected function makeSupervisorConfig(string $programName, array $data)
    {
        $default_config = [
            'process_name' => '%(program_name)s_%(process_num)02d',
            'directory' => $this->container->basePath(),
            'autostart' => true,
            'autorestart' => true,
            'user' => get_current_user(),
            'numprocs' => 1,
            'redirect_stderr' => true,
            'stdout_logfile' => "{$this->getLogsPath()}/{$this->configName()}.log",
        ];
        $data = array_merge($default_config, $data);

        $app_name = str_slug($this->getApplicationName());
        $config = "[program:$app_name-$programName]".PHP_EOL;

        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } else {
                $value = (string) $value;
            }
            $config .= "$key=$value".PHP_EOL;
        }

        return $config;
    }

    protected function getLogsPath(): string
    {
        return $this->container->make('path.storage').DIRECTORY_SEPARATOR.'logs';
    }

    /**
     * @return string
     */
    protected function configName(): string
    {
        return str_slug($this->getApplicationName().'-'.$this->processName);
    }

    protected function getApplicationName()
    {
        return config('app.name');
    }
}
