<?php
declare(strict_types = 1);

namespace NepadaTests\Texy;

use Nepada\Texy;
use Nepada\Texy\TexyFactory;
use Tester;
use Tester\Assert;


require_once __DIR__ . '/../bootstrap.php';


/**
 * @testCase
 */
class TexyMultiplierTest extends Tester\TestCase
{

    public function testMultiplier(): void
    {
        $multiplier = new Texy\TexyMultiplier('mode');
        Assert::same('mode', $multiplier->getMode());

        $multiplier->setMode('test');
        Assert::same('test', $multiplier->getMode());

        Assert::exception(
            function () use ($multiplier): void {
                $multiplier->getTexy();
            },
            Texy\InvalidStateException::class,
            "Missing Texy! factory for mode 'test'."
        );

        $multiplier->addFactory('test', new TexyFactory);
        Assert::type(\Texy\Texy::class, $multiplier->getTexy());
        Assert::same(\Texy\Texy::HTML5, $multiplier->getOutputMode());
        Assert::same('AAA', $multiplier->processTypo('AAA'));
        Assert::same('AAA', $multiplier->processLine('AAA'));
        Assert::same("<p>AAA</p>\n", $multiplier->process('AAA'));

        $texy = $multiplier->getTexy();
        $multiplier->setMode('other');
        Assert::same($texy, $multiplier->getTexy('test'));
    }

}


(new TexyMultiplierTest())->run();
