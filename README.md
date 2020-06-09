# Laminas/Mezzio + Symfony Console Bootstrap

![PHPUnit Test Suite](https://github.com/netglue/laminas-symfony-console/workflows/PHPUnit%20Test%20Suite/badge.svg)
[![codecov](https://codecov.io/gh/netglue/laminas-symfony-console/branch/master/graph/badge.svg)](https://codecov.io/gh/netglue/laminas-symfony-console)

### Introduction

This very small component provides a couple of PSR-11 factories and opinionated configuration to get Symfony’s CLI tooling bootstrapped quickly in a Mezzio application _(It might also play nice with Laminas MVC but I don’t really use that anymore…)_.

At the time of writing, I was unaware of [laminas/laminas-cli](https://github.com/laminas/laminas-cli) which does all the things this lib does and more.

**Once laminas-cli is released, I will likely kill this lib off,** so you should probably just go and check out [the official Laminas component](https://github.com/laminas/laminas-cli), that said, it hasn't been released yet so if you want something to install now, this lib will also load commands listed under `config.laminas-cli.commands`, so you can list your commands there and then swap out this library with Laminas CLI when it's released.

Anyhow, this lib assumes that you'll probably be using a PSR-11 compatible container and that you will want to use that container to lazy load your console commands.

### Installation

```bash
composer require netglue/laminas-symfony-console
```

During installation into a Mezzio app you should be asked to inject configuration in the same way as other laminas components thanks to the [Laminas component installer](https://docs.laminas.dev/laminas-component-installer/). If you choose not to do this, or want to set things up manually, then you should include `src/ConfigProvider.php` somewhere in your config.

### Handy binary…

A "binary" is shipped so that you can call `vendor/bin/cli my:command` once you're all setup. You can set the name of the application displayed on the console by setting a configuration value in `console.name`.

As environments and paths differ, this 'binary' will simply try to locate the root of project and then look for `config/container.php` in order to get hold of the container from this conventional location. There's a good chance this might not work in your setup, but making a binary like this is pretty trivial and would look similar to the following:

```php
#!/usr/bin/env/php
<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

$container = require 'path/to/file/returning/container.php';
assert($container instanceof ContainerInterface);

$app = $container->get(Application::class);
$app->run();

```

### Configuration

Commands are made available to the container-based command loader by configuring a map of `command:name => CommandName::class`, for example:

```php
return [
    'console' => [
        'name' => 'My CLI Application',
        'auto_add_invokable_factory' => true,
        'commands' => [
            'my:command' => \My\Console\DoAThing::class,
            'my:other-cmd' => \My\Console\OtherThing::class,
        ],
    ],
    'dependencies' => [
        'factories' => [
            \My\Console\DoAThing::class => \My\Console\DoAThingFactory::class,
        ],
    ],
    // Also, prefer listing commands here instead of under console.commands for future interop with laminas-cli
    'laminas-cli' => [
        'commands' => [
            'my::command' => \Some\Command::class,
        ],
    ],
];
```

### Auto-Add InvokableFactory…

It's expected that you will write factories for your commands, but the container loader factory will observe whether a) the container is an instance of the Laminas Service Manager and b) whether the feature has been enabled and automatically register an invokable factory for any command that is not present in the container.

This feature is **opt-in**, therefore you have to explicitly enable it by setting `console.auto_add_invokable_factory` to true.

_fin._
