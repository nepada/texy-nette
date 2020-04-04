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

    public function testMultiplier(): void
    {
        $multiplier = new TexyMultiplier('mode');
        Assert::same('mode', $multiplier->getMode());

        $multiplier->setMode('test');
        Assert::same('test', $multiplier->getMode());

        Assert::exception(
            function () use ($multiplier): void {
                $multiplier->getTexy();
            },
            \InvalidArgumentException::class,
            "Missing Texy! factory for mode 'test'.",
        );

        $multiplier->addFactory('test', new DefaultTexyFactory());
        Assert::type(Texy::class, $multiplier->getTexy());
        Assert::same('AAA', $multiplier->processTypo('AAA'));
        Assert::same('AAA', $multiplier->processLine('AAA'));
        Assert::same("<p>AAA</p>\n", $multiplier->process('AAA'));

        $texy = $multiplier->getTexy();
        $multiplier->setMode('other');
        Assert::same($texy, $multiplier->getTexy('test'));
    }

}


(new TexyMultiplierTest())->run();
