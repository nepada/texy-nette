<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyDI;

use Latte;
use Nepada;
use Nepada\Texy;
use Nette;

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
        /** @var \stdClass $config */
        $config = $this->getConfig();

        $container->addDefinition($this->prefix('texyFactory'))
            ->setType(Texy\TexyFactory::class);

        $container->addDefinition($this->prefix('multiplier'))
            ->setType(Texy\TexyMultiplier::class)
            ->setFactory(Texy\TexyMultiplier::class, [$config->defaultMode]);

        $container->addDefinition($this->prefix('latteFilters'))
            ->setType(Nepada\Bridges\TexyLatte\TexyFilters::class);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();
        /** @var \stdClass $config */
        $config = $this->getConfig();

        /** @var Nette\DI\ServiceDefinition $multiplier */
        $multiplier = $container->getDefinition($this->prefix('multiplier'));
        foreach ($config->factories as $name => $factory) {
            $multiplier->addSetup('addFactory', [$name, $factory]);
        }

        if (! class_exists(Latte\Engine::class)) {
            return;
        }
        $latteExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\LatteExtension::class);
        if ($latteExtension === []) {
            throw new \LogicException('LatteExtension not found, did you register it in your configuration?');
        }
        reset($latteExtension)->addMacro(Nepada\Bridges\TexyLatte\TexyModeMacro::class . '::install');

        if (! class_exists(Nepada\TemplateFactory\TemplateConfigurator::class)) {
            return;
        }
        /** @var Nette\DI\ServiceDefinition $templateConfigurator */
        $templateConfigurator = $container->getDefinitionByType(Nepada\TemplateFactory\TemplateConfigurator::class);
        $templateConfigurator->addSetup('addFilter', ['texy', [$this->prefix('@latteFilters'), 'process']])
            ->addSetup('addFilter', ['texyLine', [$this->prefix('@latteFilters'), 'processLine']])
            ->addSetup('addFilter', ['texyTypo', [$this->prefix('@latteFilters'), 'processTypo']])
            ->addSetup('addProvider', ['texy', $this->prefix('@multiplier')])
            ->addSetup('addParameter', ['texy', $this->prefix('@multiplier')]);
    }

}
