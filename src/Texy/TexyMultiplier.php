<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nette;
use Texy;

/**
 * @property-read Texy\Texy $texy
 */
class TexyMultiplier
{

    use Nette\SmartObject;

    /**
     * @var TexyFactory[]
     */
    private array $factories = [];

    /**
     * @var Texy\Texy[]
     */
    private array $texy = [];

    private string $defaultMode;

    public function __construct(string $defaultMode)
    {
        $this->defaultMode = $defaultMode;
    }

    public function addFactory(string $name, TexyFactory $factory): void
    {
        $this->factories[$name] = $factory;
    }

    public function getTexy(?string $mode = null): Texy\Texy
    {
        $mode ??= $this->defaultMode;

        if (! isset($this->texy[$mode])) {
            if (! isset($this->factories[$mode])) {
                throw new \InvalidArgumentException("Missing Texy! factory for mode '{$mode}'.");
            }
            $this->texy[$mode] = $this->factories[$mode]->create();
        }

        return $this->texy[$mode];
    }

    public function processBlock(string $text, ?string $mode = null): string
    {
        return $this->getTexy($mode)->process($text);
    }

    public function processLine(string $text, ?string $mode = null): string
    {
        return $this->getTexy($mode)->processLine($text);
    }

    public function processTypo(string $text, ?string $mode = null): string
    {
        return $this->getTexy($mode)->processTypo($text);
    }

}
