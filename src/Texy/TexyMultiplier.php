<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nette;
use Texy;


/**
 * @property-read Texy\Texy $texy
 * @property-read int $outputMode
 * @property string $mode
 */
class TexyMultiplier
{

    use Nette\SmartObject;

    /** @var ITexyFactory[] */
    private $factories;

    /** @var Texy\Texy[] */
    private $texy;

    /** @var string */
    private $mode;


    public function __construct(string $defaultMode)
    {
        $this->setMode($defaultMode);
    }

    public function addFactory(string $name, ITexyFactory $factory): void
    {
        $this->factories[$name] = $factory;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setMode(string $name): self
    {
        $this->mode = $name;
        return $this;
    }

    public function getTexy(?string $mode = null): Texy\Texy
    {
        $mode = $mode === null ? $this->mode : $mode;

        if (!isset($this->texy[$mode])) {
            if (!isset($this->factories[$mode])) {
                throw new InvalidStateException("Missing Texy! factory for mode '{$mode}'.");
            }
            $this->texy[$mode] = $this->factories[$mode]->create();
        }

        return $this->texy[$mode];
    }

    public function process(string $text, bool $singleLine = false): string
    {
        return $this->getTexy()->process($text, $singleLine);
    }

    public function processLine(string $text): string
    {
        return $this->getTexy()->processLine($text);
    }

    public function processTypo(string $text): string
    {
        return $this->getTexy()->processTypo($text);
    }

    public function getOutputMode(): int
    {
        return $this->getTexy()->getOutputMode();
    }

}
