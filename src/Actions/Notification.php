<?php

namespace Qruto\Initializer\Actions;

use Illuminate\Contracts\Container\Container;
use NunoMaduro\LaravelDesktopNotifier\Contracts\Notification as NotificationAlias;
use NunoMaduro\LaravelDesktopNotifier\Contracts\Notifier;

class Notification extends Action
{
    public function __construct(
        protected Container $container,
        protected string $title,
        protected string $body,
        protected ?string $icon = null,
    ) {
    }

    public function title(): string
    {
        return '<fg=yellow>Notify</>';
    }

    public function run(): bool
    {
        $notifier = $this->container[Notifier::class];

        $notification = $this->container[NotificationAlias::class]
            ->setTitle($this->title)
            ->setBody($this->body);

        $notification->setIcon(empty($this->icon) ? realpath(__DIR__.'/../../laravel-logo.png') : $this->icon);

        return $notifier->send($notification);
    }
}