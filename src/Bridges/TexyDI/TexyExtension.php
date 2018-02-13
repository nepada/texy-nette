<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyDI;

use Latte;
use Nepada;
use Nepada\Texy;
use Nette;


class TexyExtension extends Nette\DI\CompilerExtension
{

    /** @var mixed[] */
    public $defaults = [
        'defaultMode' => 'default',
        'factories' => [],
    ];


    public function loadConfiguration(): void
    {
        $this->defaults['factories']['default'] = $this->prefix('@texyFactory');

        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container->addDefinition($this->prefix('texyFactory'))
            ->setClass(Texy\TexyFactory::class);

        $container->addDefinition($this->prefix('multiplier'))
            ->setClass(Texy\TexyMultiplier::class)
            ->setFactory(Texy\TexyMultiplier::class, [$config['defaultMode']]);

        $container->addDefinition($this->prefix('latteFilters'))
            ->setClass(Nepada\Bridges\TexyLatte\TexyFilters::class);
    }

    public function beforeCompile(): void
    {
        $container = $this->getContainerBuilder();

        $multiplier = $container->getDefinition($this->prefix('multiplier'));
        foreach ($this->config['factories'] as $name => $factory) {
            $multiplier->addSetup('addFactory', [$name, $factory]);
        }

        if (!class_exists(Latte\Engine::class)) {
            return;
        }
        $latteExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\LatteExtension::class);
        if ($latteExtension === []) {
            throw new Texy\InvalidStateException('LatteExtension not found, did you register it in your configuration?');
        }
        reset($latteExtension)->addMacro(Nepada\Bridges\TexyLatte\TexyModeMacro::class . '::install');

        if (!class_exists(Nepada\TemplateFactory\TemplateConfigurator::class)) {
            return;
        }
        $templateConfigurator = $container->getByType(Nepada\TemplateFactory\TemplateConfigurator::class);
        if ($templateConfigurator === null) {
            throw new Texy\InvalidStateException('Could not find TemplateConfigurator service, did you register TemplateFactoryExtension in your configuration?');
        }
        $container->getDefinition($templateConfigurator)
            ->addSetup('addFilter', ['texy', [$this->prefix('@latteFilters'), 'process']])
            ->addSetup('addFilter', ['texyLine', [$this->prefix('@latteFilters'), 'processLine']])
            ->addSetup('addFilter', ['texyTypo', [$this->prefix('@latteFilters'), 'processTypo']])
            ->addSetup('addProvider', ['texy', $this->prefix('@multiplier')])
            ->addSetup('addParameter', ['texy', $this->prefix('@multiplier')]);
    }

}
