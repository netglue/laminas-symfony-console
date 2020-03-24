<?php
declare(strict_types=1);

namespace Netglue\Console;

use Symfony;
use Symfony\Component\Console\CommandLoader;

class ConfigProvider
{
    /** @return mixed[] */
    public function __invoke() : array
    {
        return [
            'console' => $this->consoleConfig(),
            'dependencies' => $this->dependencies(),
        ];
    }

    /** @return mixed[] */
    private function dependencies() : array
    {
        return [
            'factories' => [
                Symfony\Component\Console\Application::class => Container\CliApplicationFactory::class,
                CommandLoader\ContainerCommandLoader::class => Container\ContainerCommandLoaderFactory::class,
            ],
            'aliases' => [
                CommandLoader\CommandLoaderInterface::class => CommandLoader\ContainerCommandLoader::class,
            ],
        ];
    }

    /** @return mixed[] */
    private function consoleConfig() : array
    {
        return [
            'name' => 'Application Console',
            'auto_add_invokable_factory' => false,
            'commands' => [],
        ];
    }
}
