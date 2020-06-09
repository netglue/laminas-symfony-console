<?php
declare(strict_types=1);

namespace Netglue\Console\Container;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Traversable;
use function assert;
use function is_array;

class ContainerCommandLoaderFactory
{
    public function __invoke(ContainerInterface $container) : ContainerCommandLoader
    {
        $config = $container->has('config') ? $container->get('config') : [];
        assert(is_array($config) || $config instanceof Traversable);
        $commands = array_merge(
            $config['console']['commands'] ?? [],
            $config['laminas-cli']['commands'] ?? []
        );

        $autoAddInvokableFactory = $config['console']['auto_add_invokable_factory'] ?? false;
        if ($autoAddInvokableFactory) {
            $this->addInvokables($container, $commands);
        }

        return new ContainerCommandLoader($container, $commands);
    }

    /**
     * @param string[] $map
     */
    private function addInvokables(ContainerInterface $container, array $map) : void
    {
        if (! $container instanceof ServiceManager) {
            return;
        }

        foreach ($map as $name => $target) {
            if ($container->has($target)) {
                continue;
            }

            $container->setFactory($target, InvokableFactory::class);
        }
    }
}
