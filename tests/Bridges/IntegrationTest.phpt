<?php
declare(strict_types = 1);

namespace NepadaTests\Bridges;

use Nepada\Texy;
use NepadaTests\Environment;
use NepadaTests\TestCase;
use Nette;
use Nette\Utils\Strings;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class IntegrationTest extends TestCase
{

    private Nette\DI\Container $container;

    public function testServices(): void
    {
        Assert::type(Texy\TexyMultiplier::class, $this->container->getService('texy.multiplier'));
        Assert::type(Texy\DefaultTexyFactory::class, $this->container->getService('texy.texyFactory'));
    }

    /**
     * @dataProvider provideTemplateNames
     */
    public function testTemplate(string $templateName): void
    {
        $templateFile = __DIR__ . "/fixtures/{$templateName}.latte";

        $template = $this->container->getByType(Nette\Application\UI\TemplateFactory::class)->createTemplate();
        assert($template instanceof Nette\Bridges\ApplicationLatte\Template);
        $template->setFile($templateFile);

        $compiledTemplate = $template->getLatte()->compile($templateFile);
        Assert::matchFile(
            __DIR__ . "/fixtures/{$templateName}.phtml",
            $compiledTemplate,
        );

        $renderedTemplate = Strings::replace(
            $template->renderToString(),
            '~\t~',
            '    ',
        );
        Assert::matchFile(
            __DIR__ . "/fixtures/{$templateName}.html",
            $renderedTemplate,
        );
    }

    /**
     * @return array<mixed[]>
     */
    protected function provideTemplateNames(): array
    {
        return [
            ['filters.default-mode'],
            ['filters.custom-mode'],
            ['texy-block.auto-outdent'],
            ['tags.default-mode'],
            ['tags.custom-mode'],
        ];
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


(new IntegrationTest())->run();
