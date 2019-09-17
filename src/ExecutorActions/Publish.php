<?php

namespace MadWeb\Initializer\ExecutorActions;

use InvalidArgumentException;
use Illuminate\Console\Command;

class Publish
{
    private const COMMAND = 'vendor:publish';

    private $artisanCommand;

    /** @var string|array */
    private $providers;

    /** @var bool */
    private $force;

    /** @var array */
    private $arguments = [];

    public function __construct(Command $artisanCommand, $providers, bool $force = false)
    {
        $this->artisanCommand = $artisanCommand;
        $this->providers = $providers;
        $this->force = $force;
    }

    public function __invoke()
    {
        if (is_string($this->providers)) {
            $this->addProvider($this->providers);
        } elseif (is_array($this->providers)) {
            $this->handleArray();
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
        }

        foreach ($this->arguments as $argument) {
            new Artisan($this->artisanCommand, self::COMMAND, $argument);
            value(new Artisan($this->artisanCommand, self::COMMAND, $argument))();
        }
    }

    private function addProvider(string $provider, $tag = null)
    {
        $arguments['--provider'] = $provider;

        if ($tag !== null) {
            $arguments['--tag'] = $tag;
        }

        if ($this->force) {
            $arguments['--force'] = true;
        }

        $this->arguments[] = $arguments;
    }

    private function handleArray(): void
    {
        foreach ($this->providers as $key => $value) {
            if (is_numeric($key)) {
                $this->addProvider($value);
            } else {
                $this->addProvider($key, $value);
            }
        }
    }
}
