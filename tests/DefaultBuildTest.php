<?php

use Qruto\Initializer\Enums\Environment;
use Qruto\Initializer\Enums\InitializerType;

beforeEach(function () {
    require __DIR__.'/../src/build.php';
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
        actionNamesForEnvironment(InitializerType::Update, Environment::Production)
    );
});

test('update local instruction', function () {
    $this->assertEquals(
        [
            'migrate',
            'cache:clear',
            'build',
        ],
        actionNamesForEnvironment(InitializerType::Update, Environment::Local)
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
        actionNamesForEnvironment(InitializerType::Install, Environment::Production)
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
        actionNamesForEnvironment(InitializerType::Install, Environment::Local)
    );
});
