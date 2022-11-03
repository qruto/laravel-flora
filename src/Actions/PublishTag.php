<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Console\Command;
use InvalidArgumentException;

class PublishTag extends Action
{
    /**
     * @var string
     */
    protected const LOADING_TEXT = 'publishing';

    /**
     * @var string
     */
    private const COMMAND = 'vendor:publish';

    private array $arguments = [];

    private $currentArgument = [];

    /**
     * @param  string|mixed[]  $tags
     */
    public function __construct(Command $artisanCommand, private $tags, private readonly bool $force = false)
    {
        parent::__construct($artisanCommand);
    }

    public function __invoke(): bool
    {
        $errors = [];
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

            if ($this->exception) {
                $errors[] = $this->exception;
            }
        }

        $this->exception = implode(PHP_EOL, $errors);

        return true;
    }

    public function title(): string
    {
        $title = '<comment>Publish resource:</comment> ';

        $tagStringCallback = static fn (string $tag) => " Tag [$tag]";

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
        $action = new Artisan($this->getInitializerCommand(), self::COMMAND, $this->currentArgument);

        return $action->run();
    }

    private function addTag(string $tag): void
    {
        $arguments = [];
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
