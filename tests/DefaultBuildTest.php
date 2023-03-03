<?php

use Qruto\Power\Enums\Environment;
use Qruto\Power\Enums\PowerType;
use Qruto\Power\SetupInstructions;

beforeEach(function () {
    $this->app[SetupInstructions::class]->loadDefault();
});

test('update production instruction', function () {
    $this->assertEquals(
        [
            'cache',
            'migrate',
            'cache:clear',
            'queue:restart',
            'build',
        ],
        actionNamesForEnvironment(PowerType::Update, Environment::Production)
    );
});

test('update local instruction', function () {
    $this->assertEquals(
        [
            'migrate',
            'cache:clear',
            'build',
        ],
        actionNamesForEnvironment(PowerType::Update, Environment::Local)
    );
});

test('install production instruction', function () {
    $this->assertEquals(
        [
            'key:generate',
            'migrate',
            'storage:link',
            'cache',
            'build',
        ],
        actionNamesForEnvironment(PowerType::Install, Environment::Production)
    );
});

test('install local instruction', function () {
    $this->assertEquals(
        [
            'key:generate',
            'migrate',
            'storage:link',
            'build',
        ],
        actionNamesForEnvironment(PowerType::Install, Environment::Local)
    );
});
