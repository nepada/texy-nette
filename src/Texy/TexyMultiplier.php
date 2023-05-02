<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nette;
use Texy;

/**
 * @property-read Texy\Texy $texy
 */
final class TexyMultiplier
{

    use Nette\SmartObject;

    /**
     * @var array<string, TexyFactory>
     */
    private array $factories = [];

    /**
     * @var array<string, Texy\Texy>
     */
    private array $instances = [];

    private string $defaultMode;

    public function __construct(string $defaultMode)
    {
        $this->defaultMode = $defaultMode;
    }

    public function addFactory(string $name, TexyFactory $factory): void
    {
        if (isset($this->factories[$name])) {
            throw new \InvalidArgumentException("Texy! factory for mode '{$name}' is already defined.");
        }
        $this->factories[$name] = $factory;
    }

    public function getTexy(?string $mode = null): Texy\Texy
    {
        $mode ??= $this->defaultMode;

        if (! isset($this->instances[$mode])) {
            if (! isset($this->factories[$mode])) {
                throw new \InvalidArgumentException("Missing Texy! factory for mode '{$mode}'.");
            }
            $this->instances[$mode] = $this->factories[$mode]->create();
        }

        return $this->instances[$mode];
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
