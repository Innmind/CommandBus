# CommandBus

| `develop` |
|-----------|
| [![codecov](https://codecov.io/gh/Innmind/CommandBus/branch/develop/graph/badge.svg)](https://codecov.io/gh/Innmind/CommandBus) |
| [![Build Status](https://github.com/Innmind/CommandBus/workflows/CI/badge.svg)](https://github.com/Innmind/CommandBus/actions?query=workflow%3ACI) |

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
