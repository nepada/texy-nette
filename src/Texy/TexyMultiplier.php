<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nette;
use Texy;

/**
 * @property-read Texy\Texy $texy
 * @property string $mode
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

    private string $mode;

    public function __construct(string $defaultMode)
    {
        $this->mode = $defaultMode;
    }

    public function addFactory(string $name, TexyFactory $factory): void
    {
        $this->factories[$name] = $factory;
    }

    /**
     * @deprecated will be removed without replacement
     * @return string
     */
    public function getMode(): string
    {
        trigger_error('Changing internal mode of TexyMultiplier is deprecated, TexyMultiplier::getMode() will be removed', E_USER_DEPRECATED);
        return $this->mode;
    }

    /**
     * @deprecated specify mode explicitly in process*() method
     * @param string $name
     * @return static
     */
    public function setMode(string $name): self
    {
        trigger_error('Changing internal mode of TexyMultiplier is deprecated, pass custom mode explicitly to process*() method', E_USER_DEPRECATED);
        $this->mode = $name;
        return $this;
    }

    public function getTexy(?string $mode = null): Texy\Texy
    {
        $mode ??= $this->mode;

        if (! isset($this->texy[$mode])) {
            if (! isset($this->factories[$mode])) {
                throw new \InvalidArgumentException("Missing Texy! factory for mode '{$mode}'.");
            }
            $this->texy[$mode] = $this->factories[$mode]->create();
        }

        return $this->texy[$mode];
    }

    /**
     * @deprecated use processBlock() or processLine() instead
     * @param string $text
     * @param bool $singleLine
     * @return string
     */
    public function process(string $text, bool $singleLine = false): string
    {
        trigger_error('TexyMultiplier::process() is deprecated, use processBlock() or processLine() instead', E_USER_DEPRECATED);
        return $this->getTexy()->process($text, $singleLine);
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
