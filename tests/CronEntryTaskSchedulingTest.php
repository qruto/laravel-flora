<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Process;
use Mockery\MockInterface;
use Qruto\Flora\SetupInstructions;

function prepare()
{
    return new class()
    {
        public string $command;

        public function __construct()
        {
            app()->detectEnvironment(fn () => 'production');

            $this->command = sprintf(
                '(crontab -l 2>/dev/null; echo "%s") | crontab -',
                sprintf('* * * * * cd %s && php artisan schedule:run >> /dev/null 2>&1', base_path())
            );

            test()->mock(SetupInstructions::class, fn (MockInterface $mock) => $mock
                ->shouldReceive('customExists')
                ->once()
                ->andReturn(true)
                ->shouldReceive('load')
                ->once()
            );

            app(Schedule::class)->command('inspire')->hourly();

            App::install('production', fn ($run) => $run->call(fn () => null));
        }

        public function run()
        {
            return test()->artisan('install');
        }

        public function fake(array $input)
        {
            Process::fake($input);
        }

        public function assertRan(string $command)
        {
            Process::assertRan($command);
        }

        public function assertNotRan(string $command)
        {
            Process::assertNotRan($command);
        }
    };
}

it('adds cron entry to crontab', function () {
    $crontab = prepare();

    $crontab->fake([
        'crontab -l' => 'crontab: no crontab for user',
        $crontab->command,
    ]);

    $crontab->run()
        ->expectsConfirmation('Add a cron entry for task scheduling?', 'yes')
        ->expectsOutputToContain('Entry was added');

    $crontab->assertRan($crontab->command);
});

it('don\'t add cron entry if to crontab when not permitted', function () {
    $crontab = prepare();

    $crontab->fake([
        'crontab -l' => 'crontab: no crontab for user',
        $crontab->command,
    ]);

    $crontab->run()
        ->expectsConfirmation('Add a cron entry for task scheduling?', 'no')
        ->doesntExpectOutput('Entry was added');

    $crontab->assertNotRan($crontab->command);
});

it('shows warning if the entry has already added', function () {
    $crontab = prepare();

    $crontab->fake([
        'crontab -l' => $crontab->command,
    ]);

    $crontab->run()
        ->expectsOutputToContain('WARN  Cron entry for task scheduling already exists.');

    $crontab->assertNotRan($crontab->command);
});
