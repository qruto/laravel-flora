<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;
use InvalidArgumentException;

class Publish extends Action
{
    protected const LOADING_TEXT = 'publishing';

    private const COMMAND = 'vendor:publish';

    /** @var string|array */
    private $providers;

    /** @var bool */
    private $force;

    /** @var array */
    private $arguments = [];

    private $currentArgument = [];

    public function __construct(Command $artisanCommand, $providers, bool $force = false)
    {
        parent::__construct($artisanCommand);

        $this->providers = $providers;
        $this->force = $force;
    }

    public function __invoke(): bool
    {
        if (is_string($this->providers)) {
            $this->addProvider($this->providers);
        } elseif (is_array($this->providers)) {
            $this->handleArray();
        } else {
            throw new InvalidArgumentException('Invalid publishable argument.');
        }

        foreach ($this->arguments as $argument) {
            $this->currentArgument = $argument;

            $errors = [];
            parent::__invoke();

            if ($this->errorMessage) {
                $errors[] = $this->errorMessage;
            }
        }

        $this->errorMessage = implode(PHP_EOL, $errors);

        return true;
    }

    public function title(): string
    {
        $title = '<comment>Publish resource:</comment> ';

        if (isset($this->currentArgument['--provider'])) {
            $title .= "Provider [{$this->currentArgument['--provider']}]";
        }

        $tagStringCallback = function (string $tag) {
            return " Tag [$tag]";
        };

        if (isset($this->currentArgument['--tag'])) {
            if (is_string($this->currentArgument['--tag'])) {
                $title .= $tagStringCallback($this->currentArgument['--tag']);
            } else {
                foreach ($this->currentArgument['--tag'] as $tag) {
                    $title .= $tagStringCallback($tag);
                }
            }
        }

        return $title;
    }

    public function run(): bool
    {
        $action = new Artisan($this->getArtisanCommnad(), self::COMMAND, $this->currentArgument);

        return $action->run();
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
