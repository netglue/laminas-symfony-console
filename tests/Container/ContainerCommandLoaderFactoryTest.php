<?php
declare(strict_types=1);

namespace Netglue\ConsoleTest\Container;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Netglue\Console\Container\ContainerCommandLoaderFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\HelpCommand;

class ContainerCommandLoaderFactoryTest extends TestCase
{
    /** @var ObjectProphecy|ContainerInterface */
    private $container;
    /** @var ServiceManager|ObjectProphecy */
    private $serviceManager;

    protected function setUp() : void
    {
        parent::setUp();
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->serviceManager = $this->prophesize(ServiceManager::class);
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

    /** @param mixed[] $config */
    private function serviceManagerWillReturnConfig(array $config) : void
    {
        $this->serviceManager->has('config')
            ->shouldBeCalled()
            ->willReturn(true);
        $this->serviceManager->get('config')
            ->shouldBeCalled()
            ->willReturn($config);
    }

    public function testThatAutoInvokablesWillNotBeAddedWhenContainerIsNotLaminasInstance() : void
    {
        $this->containerWillReturnConfig([
            'console' => [
                'auto_add_invokable_factory' => true,
                'commands' => [
                    'whatever' => HelpCommand::class,
                ],
            ],
        ]);
        $this->container->has(HelpCommand::class)->shouldNotBeCalled();
        $factory = new ContainerCommandLoaderFactory();
        $loader = $factory->__invoke($this->container->reveal());
        $this->assertFalse($loader->has(HelpCommand::class));
    }

    public function testInvokableFactoryIsNotAddedWhenAlreadyPresentInContainer() : void
    {
        $this->serviceManagerWillReturnConfig([
            'console' => [
                'auto_add_invokable_factory' => true,
                'commands' => [
                    'whatever' => HelpCommand::class,
                ],
            ],
        ]);

        $this->serviceManager->has(HelpCommand::class)
            ->shouldBeCalled()
            ->willReturn(true);

        $this->serviceManager->setFactory(Argument::any(), Argument::any())
            ->shouldNotBeCalled();
        $factory = new ContainerCommandLoaderFactory();
        $factory->__invoke($this->serviceManager->reveal());
    }

    public function testInvokableFactoryIsAddedWhenAppropriate() : void
    {
        $this->serviceManagerWillReturnConfig([
            'console' => [
                'auto_add_invokable_factory' => true,
                'commands' => [
                    'whatever' => HelpCommand::class,
                ],
            ],
        ]);

        $this->serviceManager->has(HelpCommand::class)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->serviceManager->setFactory(HelpCommand::class, InvokableFactory::class)
            ->shouldBeCalled();
        $factory = new ContainerCommandLoaderFactory();
        $factory->__invoke($this->serviceManager->reveal());
    }
}
