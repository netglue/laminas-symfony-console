<?php
declare(strict_types=1);

namespace Netglue\ConsoleTest\Container;

use Netglue\Console\Container\CliApplicationFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\CommandLoader\FactoryCommandLoader;

class CliApplicationFactoryTest extends TestCase
{
    /** @var ObjectProphecy|ContainerInterface */
    private $container;

    protected function setUp() : void
    {
        parent::setUp();
        $this->container = $this->prophesize(ContainerInterface::class);
    }

    /** @param mixed[] $config */
    private function containerWillReturnConfig(array $config) : void
    {
        $this->container->has('config')
            ->shouldBeCalled()
            ->willReturn(true);
        $this->container->get('config')
            ->shouldBeCalled()
            ->willReturn($config);
    }

    private function injectLoader() : void
    {
        $this->container->get(CommandLoaderInterface::class)
            ->willReturn(new FactoryCommandLoader([]))
            ->shouldBeCalled();
    }

    public function testApplicationHasDefaultName() : void
    {
        $this->containerWillReturnConfig([]);
        $this->injectLoader();

        $factory = new CliApplicationFactory();
        $cli = $factory->__invoke($this->container->reveal());
        $this->assertNotNull($cli->getName());
    }

    public function testApplicationNameWillBeSetViaConfig() : void
    {
        $this->containerWillReturnConfig(['console' => ['name' => 'My CLI App']]);
        $this->injectLoader();
        $factory = new CliApplicationFactory();
        $cli = $factory->__invoke($this->container->reveal());
        $this->assertSame('My CLI App', $cli->getName());
    }
}
