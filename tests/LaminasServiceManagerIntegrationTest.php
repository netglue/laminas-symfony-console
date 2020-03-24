<?php
declare(strict_types=1);

namespace Netglue\ConsoleTest;

use Laminas\ServiceManager\ServiceManager;
use Netglue\Console\ConfigProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

class LaminasServiceManagerIntegrationTest extends TestCase
{
    /** @return mixed[] */
    private function defaultConfig() : array
    {
        $config = (new ConfigProvider())();
        $config['dependencies']['services']['config'] = $config;

        return $config;
    }

    /** @param mixed[] $config */
    private function container(array $config) : ServiceManager
    {
        return new ServiceManager($config['dependencies']);
    }

    public function testDefaultConfigWillYieldACliApplication() : void
    {
        $container = $this->container($this->defaultConfig());
        $this->assertTrue($container->has(Application::class));
        $container->get(Application::class);
    }
}
