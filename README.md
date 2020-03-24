# Laminas/Mezzio + Symfony Console Bootstrap

### Introduction

This very small component provides a couple of PSR-11 factories and opinionated configuration to get Symfony’s CLI tooling bootstrapped quickly in a Mezzio application _(It might also play nice with Laminas MVC but I don’t really use that anymore…)_.

It assumes that you'll probably be using a PSR-11 compatible container and that you will want to use that container to lazy load your console commands.

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
];
```

### Auto-Add InvokableFactory…

It's expected that you will write factories for your commands, but the container loader factory will observe whether a) the container is an instance of the Laminas Service Manager and b) whether the feature has been enabled and automatically register an invokable factory for any command that is not present in the container.

This feature is **opt-in**, therefore you have to explicitly enable it by setting `console.auto_add_invokable_factory` to true.

_fin._
