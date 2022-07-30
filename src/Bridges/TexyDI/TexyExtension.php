<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyDI;

use Nepada;
use Nepada\Texy;
use Nette;
use Nette\Bridges\ApplicationDI\LatteExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;

/**
 * @property \stdClass $config
 */
final class TexyExtension extends Nette\DI\CompilerExtension
{

    private const DEFAULT_MODE = 'default';

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'defaultMode' => Nette\Schema\Expect::string(self::DEFAULT_MODE),
            'factories' => Nette\Schema\Expect::array()->default([
                self::DEFAULT_MODE => $this->prefix('@texyFactory'),
            ]),
        ]);
    }

    public function loadConfiguration(): void
    {
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('texyFactory'), new ServiceDefinition())
            ->setType(Texy\DefaultTexyFactory::class);

        $container->addDefinition($this->prefix('multiplier'), new ServiceDefinition())
            ->setType(Texy\TexyMultiplier::class)
            ->setFactory(Texy\TexyMultiplier::class, [$this->config->defaultMode]);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        $multiplier = $container->getDefinition($this->prefix('multiplier'));
        assert($multiplier instanceof ServiceDefinition);
        foreach ($this->config->factories as $name => $factory) {
            $multiplier->addSetup('addFactory', [$name, $factory]);
        }

        /** @var LatteExtension $latteExtension */
        foreach ($this->compiler->getExtensions(LatteExtension::class) as $latteExtension) {
            $latteExtension->addExtension(new Statement(
                Nepada\Bridges\TexyLatte\TexyLatteExtension::class,
                [
                    'texyMultiplier' => $multiplier,
                ],
            ));
        }
    }

}
