<?php

namespace MadWeb\Initializer\Actions;

use Illuminate\Console\Command;

abstract class Action
{
    /** @var \Illuminate\Console\Command */
    private $artisanCommnad;

    public function __construct(Command $artisanCommnad)
    {
        $this->artisanCommnad = $artisanCommnad;
    }

    public function __invoke(): bool
    {
        return $this->getArtisanCommnad()->task($this->title(), function () {
            return $this->run();
        });
    }

    abstract public function title(): string;

    abstract public function run(): bool;

    abstract public function message(): string;

    public function getArtisanCommnad(): Command
    {
        return $this->artisanCommnad;
    }
}
