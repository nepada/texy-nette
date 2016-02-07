<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace NepadaTests\Bridges\TexyDI;

use Nepada\Texy;
use Nette;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class TexyExtensionTest extends Tester\TestCase
{

    /** @var Nette\DI\Container */
    private $container;


    public function setUp()
    {
        $configurator = new Nette\Configurator;
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

    public function testServices()
    {
        Assert::type(Texy\TexyMultiplier::class, $this->container->getService('texy.multiplier'));
        Assert::type(Texy\TexyFactory::class, $this->container->getService('texy.texyFactory'));
    }

    public function testTemplate()
    {
        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate();
        $template->setFile(__DIR__ . '/fixtures/test.latte');

        Assert::matchFile(
            __DIR__ . '/fixtures/test.phtml',
            $template->getLatte()->compile($template->getFile())
        );

        Assert::matchFile(
            __DIR__ . '/fixtures/test.html',
            (string) $template
        );
    }

}


\run(new TexyExtensionTest());
