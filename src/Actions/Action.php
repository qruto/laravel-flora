<?php

namespace Qruto\Flora\Actions;

use Exception;
use Illuminate\Console\View\Components\Factory;
use Qruto\Flora\Console\StopSetupException;
use function strlen;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

abstract class Action
{
    /** Exception thrown during an action */
    protected ?Throwable $exception = null;

    /** Label for the action */
    public static string $label;

    /** Color for the action label */
    protected string $color = 'white';

    /** Whether the action was terminated */
    protected bool $terminated = false;

    /** Whether the action was successful */
    protected bool $successful = false;

    /** Whether the action should be silent */
    protected bool $silent = false;

    /** Current command environment output interface */
    protected OutputInterface $output;

    /** Perform the action */
    public function __invoke(Factory $outputComponents, int $labelWidth): bool
    {
        $callback = function (): bool {
            try {
                return $this->successful = $this->run();
            } catch (ActionTerminatedException $e) {
                $this->terminated = true;

                return $this->successful = true;
            } catch (StopSetupException $e) {
                throw $e;
            } catch (Exception $e) {
                $this->exception = $e;

                return $this->successful = false;
            }
        };

        if ($this->silent) {
            return $callback();
        }

        $outputComponents->task($this->title($labelWidth), $callback);

        if ($this->terminated) {
            $this->output->write("\x1B[1A");
            $this->output->write("\x1B[2K");
        }

        return $this->failed();
    }

    /** Determines whether the action has failed */
    public function failed(): bool
    {
        return ! $this->successful;
    }

    /** Determines whether the action has been terminated */
    public function terminated(): bool
    {
        return $this->terminated;
    }

    /** Get exception thrown during performing */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /** Set the output interface */
    public function withOutput(OutputInterface $output): static
    {
        $this->output = $output;

        return $this;
    }

    /** Returns spaces string required to format label width */
    private function spaces(string $title, int $width): string
    {
        if ($width === 0) {
            return '';
        }

        return str_repeat(' ', $width - strlen($title));
    }

    /** Returns formatted action title */
    public function title(int $width = 0): string
    {
        $name = $this->name();
        $title = static::$label;
        $description = $this->description();

        $spaces = $this->spaces($title, $width);
        $title = "<fg={$this->color};options=bold>$title</>$spaces $name";

        if ($description !== '' && $description !== '0') {
            $title .= " <fg=gray>$description</>";
        }

        return $title;
    }

    /** Returns action name */
    abstract public function name(): string;

    /** Returns action description */
    protected function description(): string
    {
        return '';
    }

    /** Run action specific needs */
    abstract public function run(): bool;

    /** Determines whether action is silent */
    public function isSilent(): bool
    {
        return $this->silent;
    }
}
