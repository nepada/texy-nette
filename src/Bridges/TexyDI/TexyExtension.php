<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Bridges\TexyDI;

use Latte;
use Nepada;
use Nepada\Texy;
use Nette;


class TexyExtension extends Nette\DI\CompilerExtension
{

    /** @var array */
    public $defaults = [
        'defaultMode' => 'default',
        'factories' => [],
    ];


    public function loadConfiguration()
    {
        $this->defaults['factories']['default'] = $this->prefix('@texyFactory');

        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container->addDefinition($this->prefix('texyFactory'))
            ->setClass(Texy\TexyFactory::class);

        $container->addDefinition($this->prefix('multiplier'))
            ->setClass(Texy\TexyMultiplier::class, [$config['defaultMode']]);
    }

    public function beforeCompile()
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
        if (!$latteExtension) {
            throw new Texy\InvalidStateException('LatteExtension not found, did you register it in your configuration?');
        }
        reset($latteExtension)->addMacro(Nepada\Bridges\TexyLatte\TexyModeMacro::class . '::install');

        if (!class_exists(Nepada\TemplateFactory\TemplateConfigurator::class)) {
            return;
        }
        $templateConfigurator = $container->getByType(Nepada\TemplateFactory\TemplateConfigurator::class);
        if (!$templateConfigurator) {
            throw new Texy\InvalidStateException('Could not find TemplateConfigurator service, did you register TemplateFactoryExtension in your configuration?');
        }
        $container->getDefinition($templateConfigurator)
            ->addSetup('addFilter', ['texy', [$this->prefix('@multiplier'), 'process']])
            ->addSetup('addFilter', ['texyLine', [$this->prefix('@multiplier'), 'processLine']])
            ->addSetup('addFilter', ['texyTypo', [$this->prefix('@multiplier'), 'processTypo']])
            ->addSetup('addParameter', ['_texy', $this->prefix('@multiplier')])
            ->addSetup('addParameter', ['texy', $this->prefix('@multiplier')]);
    }

}
