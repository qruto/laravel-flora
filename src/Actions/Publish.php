<?php

namespace MadWeb\Initializer\Actions;

use InvalidArgumentException;

class Publish
{
    public const COMMAND = 'vendor:publish';

    /** @var string|array */
    private $providers;

    /** @var bool */
    private $force;

    /** @var array */
    private $arguments = [];

    public function __construct($providers, bool $force = false)
    {
        $this->providers = $providers;
        $this->force = $force;
    }

    public function handle()
    {
        if (is_string($this->providers)) {
            $this->addProvider($this->providers);
        } elseif (is_array($this->providers)) {
            $this->handleArray();
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
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

    public function getArguments(): array
    {
        return $this->arguments;
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
