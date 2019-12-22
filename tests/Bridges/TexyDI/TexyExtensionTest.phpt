<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TexyDI;

use Nepada\Texy;
use NepadaTests\TestCase;
use Nette;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';


/**
 * @testCase
 */
class TexyExtensionTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testServices(): void
    {
        Assert::type(Texy\TexyMultiplier::class, $this->container->getService('texy.multiplier'));
        Assert::type(Texy\TexyFactory::class, $this->container->getService('texy.texyFactory'));
    }

    public function testTemplate(): void
    {
        $templateFile = __DIR__ . '/fixtures/test.latte';

        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate();
        $template->setFile($templateFile);

        Assert::matchFile(
            __DIR__ . '/fixtures/test.phtml',
            $template->getLatte()->compile($templateFile),
        );

        Assert::matchFile(
            __DIR__ . '/fixtures/test.html',
            (string) $template,
        );
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(TEMP_DIR);
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new TexyExtensionTest())->run();
