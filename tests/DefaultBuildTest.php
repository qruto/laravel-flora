<?php

use Qruto\Formula\Enums\Environment;
use Qruto\Formula\Enums\FormulaType;

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
        actionNamesForEnvironment(FormulaType::Update, Environment::Production)
    );
});

test('update local instruction', function () {
    $this->assertEquals(
        [
            'migrate',
            'cache:clear',
            'build',
        ],
        actionNamesForEnvironment(FormulaType::Update, Environment::Local)
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
        actionNamesForEnvironment(FormulaType::Install, Environment::Production)
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
        actionNamesForEnvironment(FormulaType::Install, Environment::Local)
    );
});
