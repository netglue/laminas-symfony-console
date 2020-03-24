<?php
declare(strict_types=1);

namespace Netglue\Console\Container;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Traversable;
use function assert;
use function is_array;

class CliApplicationFactory
{
    public function __invoke(ContainerInterface $container) : Application
    {
        $config = $container->has('config') ? $container->get('config') : [];
        assert(is_array($config) || $config instanceof Traversable);

        $config = $config['console'] ?? [];

        $name = $config['name'] ?? 'CLI Application';
        $app = new Application($name);
        $loader = $container->get(CommandLoaderInterface::class);
        $app->setCommandLoader($loader);

        return $app;
    }
}
