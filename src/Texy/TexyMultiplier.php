<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

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
    public function __construct($defaultMode)
    {
        $this->setMode($defaultMode);
    }

    /**
     * @param string $name
     * @param ITexyFactory $factory
     */
    public function addFactory($name, ITexyFactory $factory)
    {
        $this->factories[$name] = $factory;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $name
     * @return static
     */
    public function setMode($name)
    {
        $this->mode = (string) $name;
        return $this;
    }

    /**
     * @param string|null $mode
     * @return Texy\Texy
     */
    public function getTexy($mode = null)
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
    public function process($text, $singleLine = false)
    {
        return $this->getTexy()->process($text, $singleLine);
    }

    /**
     * @param string $text
     * @return string
     */
    public function processLine($text)
    {
        return $this->getTexy()->processLine($text);
    }

    /**
     * @param string $text
     * @return string
     */
    public function processTypo($text)
    {
        return $this->getTexy()->processTypo($text);
    }

    /**
     * @return int
     */
    public function getOutputMode()
    {
        return $this->getTexy()->getOutputMode();
    }

}
