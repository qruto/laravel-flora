<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;
use InvalidArgumentException;

class PublishTag extends Action
{
    protected const LOADING_TEXT = 'publishing';

    private const COMMAND = 'vendor:publish';

    /** @var string|array */
    private $tags;

    /** @var bool */
    private $force;

    /** @var array */
    private $arguments = [];

    private $currentArgument = [];

    public function __construct(Command $artisanCommand, $tags, bool $force = false)
    {
        parent::__construct($artisanCommand);

        $this->tags = $tags;
        $this->force = $force;
    }

    public function __invoke(): bool
    {
        if (is_string($this->tags)) {
            $this->addTag($this->tags);
        } elseif (is_array($this->tags)) {
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

        return trim($title);
    }

    public function run(): bool
    {
        $action = new Artisan($this->getArtisanCommnad(), self::COMMAND, $this->currentArgument);

        return $action->run();
    }

    private function addTag(string $tag)
    {
        $arguments['--tag'] = $tag;

        if ($this->force) {
            $arguments['--force'] = true;
        }

        $this->arguments[] = $arguments;
    }

    private function handleArray(): void
    {
        foreach ($this->tags as $tag) {
            $this->addTag($tag);
        }
    }
}
