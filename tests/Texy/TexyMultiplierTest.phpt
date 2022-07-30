<?php
declare(strict_types = 1);

namespace NepadaTests\Texy;

use Nepada\Texy\DefaultTexyFactory;
use Nepada\Texy\TexyMultiplier;
use NepadaTests\TestCase;
use Tester\Assert;
use Texy\Texy;

require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TexyMultiplierTest extends TestCase
{

    public function testProcessing(): void
    {
        $multiplier = new TexyMultiplier('default');

        $multiplier->addFactory('test', new DefaultTexyFactory());
        Assert::type(Texy::class, $multiplier->getTexy('test'));
        Assert::same('**AAA**', $multiplier->processTypo('**AAA**', 'test'));
        Assert::same('<strong>AAA</strong>', $multiplier->processLine('**AAA**', 'test'));
        Assert::same("<p>AAA</p>\n", $multiplier->processBlock('AAA', 'test'));

        $multiplier->addFactory('default', new DefaultTexyFactory());
        Assert::type(Texy::class, $multiplier->getTexy());
        Assert::same('**AAA**', $multiplier->processTypo('**AAA**'));
        Assert::same('<strong>AAA</strong>', $multiplier->processLine('**AAA**'));
        Assert::same("<p>AAA</p>\n", $multiplier->processBlock('AAA'));
    }

    public function testInvalidMode(): void
    {
        $multiplier = new TexyMultiplier('default');

        Assert::exception(
            function () use ($multiplier): void {
                $multiplier->getTexy();
            },
            \InvalidArgumentException::class,
            "Missing Texy! factory for mode 'default'.",
        );

        Assert::exception(
            function () use ($multiplier): void {
                $multiplier->getTexy('also-missing');
            },
            \InvalidArgumentException::class,
            "Missing Texy! factory for mode 'also-missing'.",
        );
    }

}


(new TexyMultiplierTest())->run();
