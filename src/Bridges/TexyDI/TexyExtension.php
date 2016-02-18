<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Bridges\TexyDI;

use Nepada;
use Nepada\Texy;
use Nette;
use Latte;


class TexyExtension extends Nette\DI\CompilerExtension
{

    /** @var array */
    public $defaults = [
        'defaultMode' => 'default',
        'factories' => [],
    ];


    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->validateConfig($this->defaults);

        $container->addDefinition($this->prefix('texyFactory'))
            ->setClass(Texy\TexyFactory::class);

        $multiplier = $container->addDefinition($this->prefix('multiplier'))
            ->setClass(Texy\TexyMultiplier::class, [$config['defaultMode']])
            ->addSetup('addFactory', ['default', $this->prefix('@texyFactory')]);

        foreach ($config['factories'] as $name => $factory) {
            $multiplier->addSetup('addFactory', [$name, $factory]);
        }
    }

    public function beforeCompile()
    {
        $container = $this->getContainerBuilder();

        if (!class_exists(Latte\Engine::class)) {
            return;
        }

        $latteExtension = $this->compiler->getExtensions(Nette\Bridges\ApplicationDI\LatteExtension::class);
        if (!$latteExtension) {
            throw new Texy\InvalidStateException("LatteExtension not found, did you register it in your configuration?");
        }
        reset($latteExtension)->addMacro(Nepada\Bridges\TexyLatte\TexyModeMacro::class . '::install');

        $templateConfigurator = $container->getByType(Nepada\TemplateFactory\TemplateConfigurator::class);
        if (!$templateConfigurator) {
            throw new Texy\InvalidStateException("Could not find TemplateConfigurator service, have you installed and enabled nepada/template-factory?");
        }
        $container->getDefinition($templateConfigurator)
            ->addSetup('addFilter', array('texy', array($this->prefix('@multiplier'), 'process')))
            ->addSetup('addFilter', array('texyLine', array($this->prefix('@multiplier'), 'processLine')))
            ->addSetup('addFilter', array('texyTypo', array($this->prefix('@multiplier'), 'processTypo')))
            ->addSetup('addParameter', ['_texy', $this->prefix('@multiplier')])
            ->addSetup('addParameter', ['texy', $this->prefix('@multiplier')]);
    }

}
