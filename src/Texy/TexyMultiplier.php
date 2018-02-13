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


    /**
     * @param string $defaultMode
     */
    public function __construct(string $defaultMode)
    {
        $this->setMode($defaultMode);
    }

    /**
     * @param string $name
     * @param ITexyFactory $factory
     */
    public function addFactory(string $name, ITexyFactory $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * @return string
     */
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

    /**
     * @param string|null $mode
     * @return Texy\Texy
     */
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

    /**
     * @param string $text
     * @param bool $singleLine
     * @return string
     */
    public function process(string $text, bool $singleLine = false): string
    {
        return $this->getTexy()->process($text, $singleLine);
    }

    /**
     * @param string $text
     * @return string
     */
    public function processLine(string $text): string
    {
        return $this->getTexy()->processLine($text);
    }

    /**
     * @param string $text
     * @return string
     */
    public function processTypo(string $text): string
    {
        return $this->getTexy()->processTypo($text);
    }

    /**
     * @return int
     */
    public function getOutputMode(): int
    {
        return $this->getTexy()->getOutputMode();
    }

}
