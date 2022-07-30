<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyDI;

use Nepada;
use Nepada\Texy;
use Nette;
use Nette\DI\Definitions\ServiceDefinition;

class TexyExtension extends Nette\DI\CompilerExtension
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
        $config = $this->getConfig();
        assert($config instanceof \stdClass);

        $container->addDefinition($this->prefix('texyFactory'), new ServiceDefinition())
            ->setType(Texy\DefaultTexyFactory::class);

        $container->addDefinition($this->prefix('multiplier'), new ServiceDefinition())
            ->setType(Texy\TexyMultiplier::class)
            ->setFactory(Texy\TexyMultiplier::class, [$config->defaultMode]);

        $container->addDefinition($this->prefix('latteFilters'), new ServiceDefinition())
            ->setType(Nepada\Bridges\TexyLatte\TexyFilters::class);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        assert($config instanceof \stdClass);

        $multiplier = $container->getDefinition($this->prefix('multiplier'));
        assert($multiplier instanceof ServiceDefinition);
        foreach ($config->factories as $name => $factory) {
            $multiplier->addSetup('addFactory', [$name, $factory]);
        }

        if (! class_exists(Nepada\TemplateFactory\TemplateConfigurator::class)) {
            return;
        }
        $templateConfigurator = $container->getDefinitionByType(Nepada\TemplateFactory\TemplateConfigurator::class);
        assert($templateConfigurator instanceof ServiceDefinition);
        $templateConfigurator->addSetup('addFilter', ['texy', [$this->prefix('@latteFilters'), 'process']])
            ->addSetup('addFilter', ['texyLine', [$this->prefix('@latteFilters'), 'processLine']])
            ->addSetup('addFilter', ['texyTypo', [$this->prefix('@latteFilters'), 'processTypo']])
            ->addSetup('addProvider', ['texy', $this->prefix('@multiplier')])
            ->addSetup('addParameter', ['texy', $this->prefix('@multiplier')]);
    }

}
