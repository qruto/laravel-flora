<?php

namespace MadWeb\Initializer\Actions;

use InvalidArgumentException;
use Illuminate\Console\Command;

class Publish extends Action
{
    private const COMMAND = 'vendor:publish';

    /** @var string|array */
    private $providers;

    /** @var bool */
    private $force;

    /** @var array */
    private $arguments = [];

    public function __construct(Command $artisanCommand, $providers, bool $force = false)
    {
        parent::__construct($artisanCommand);

        $this->providers = $providers;
        $this->force = $force;
    }

    public function title(): string
    {
        return '';
    }

    public function message(): string
    {
        return '';
    }

    public function run(): bool
    {
        if (is_string($this->providers)) {
            $this->addProvider($this->providers);
        } elseif (is_array($this->providers)) {
            $this->handleArray();
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
        }

        $result = true;
        foreach ($this->arguments as $argument) {
            $result = value(new Artisan($this->getArtisanCommnad(), self::COMMAND, $argument))();
        }

        return $result;
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
