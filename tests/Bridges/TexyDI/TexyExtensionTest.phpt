<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges\TexyDI;

use Nepada\Texy;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Nette\Utils\Strings;
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
        Assert::type(Texy\DefaultTexyFactory::class, $this->container->getService('texy.texyFactory'));
    }

    public function testTemplate(): void
    {
        $templateFile = __DIR__ . '/fixtures/test.latte';

        /** @var Nette\Bridges\ApplicationLatte\Template $template */
        $template = $this->container->getByType(Nette\Application\UI\ITemplateFactory::class)->createTemplate();
        $template->setFile($templateFile);

        // Normalize filter name case to maintain BC
        $compiledTemplate = Strings::replace(
            @$template->getLatte()->compile($templateFile),
            ['~texyTypo~' => 'texytypo', '~texyLine~' => 'texyline'],
        );
        Assert::matchFile(
            __DIR__ . '/fixtures/test.phtml',
            $compiledTemplate,
        );

        Assert::matchFile(
            __DIR__ . '/fixtures/test.html',
            @$template->renderToString(),
        );
    }

    protected function setUp(): void
    {
        $configurator = new Nette\Configurator();
        $configurator->setTempDirectory(Environment::getTempDir());
        $configurator->setDebugMode(true);
        $configurator->addConfig(__DIR__ . '/fixtures/config.neon');
        $this->container = $configurator->createContainer();
    }

}


(new TexyExtensionTest())->run();
