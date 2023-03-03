<?php

use Qruto\Flora\Enums\Environment;
use Qruto\Flora\Enums\FloraType;
use Qruto\Flora\SetupInstructions;

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
        actionNamesForEnvironment(FloraType::Update, Environment::Production)
    );
});

test('update local instruction', function () {
    $this->assertEquals(
        [
            'migrate',
            'cache:clear',
            'build',
        ],
        actionNamesForEnvironment(FloraType::Update, Environment::Local)
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
        actionNamesForEnvironment(FloraType::Install, Environment::Production)
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
        actionNamesForEnvironment(FloraType::Install, Environment::Local)
    );
});
