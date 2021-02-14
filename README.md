# CommandBus

[![Build Status](https://github.com/innmind/commandbus/workflows/CI/badge.svg?branch=master)](https://github.com/innmind/commandbus/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/innmind/commandbus/branch/develop/graph/badge.svg)](https://codecov.io/gh/innmind/commandbus)
[![Type Coverage](https://shepherd.dev/github/innmind/commandbus/coverage.svg)](https://shepherd.dev/github/innmind/commandbus)

Simple library to route a command to its handler, the interface allows you to compose buses to add capabilities. Each handler must be a `callable`.

## Installation

```sh
composer require innmind/command-bus
```

## Example

```php
use function Innmind\CommandBus\bootstrap;
use Innmind\Immutable\Map;

class MyCommand {}

$echo = function(MyCommand $command) {
    echo 'foo';
};

$handle = bootstrap()['bus'](
    Map::of('string', 'callable')
        (MyCommand::class, $echo)
);

$handle(new MyCommand); //prints 'foo' and return null;
```
