# CommandBus

| `master` | `develop` |
|----------|-----------|
| [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Innmind/CommandBus/?branch=master) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/CommandBus/?branch=develop) |
| [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Innmind/CommandBus/?branch=master) | [![Code Coverage](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/CommandBus/?branch=develop) |
| [![Build Status](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Innmind/CommandBus/build-status/master) | [![Build Status](https://scrutinizer-ci.com/g/Innmind/CommandBus/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/Innmind/CommandBus/build-status/develop) |

Simple library to route a command to its handler, the interface allows you to compose buses to add capabilities. Each handler must be a `callable`.

## Installation

```sh
composer require innmind/command-bus
```

## Example

```php
use Innmind\{
    CommandBus\CommandBus,
    Immutable\Map
};

class MyCommand {}

$bus = new CommandBus(
    (new Map('string', 'callable'))
        ->put(
            MyCommand::class,
            function(MyCommand $command) {
                echo 'foo';
            }
        )
);

$bus->handle(new MyCommand); //prints 'foo' and return null;
```
