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
 * @property string $mode
 * @method string process(string $text, bool $singleLine = false)
 * @method string processLine(string $text)
 * @method string processTypo(string $text)
 */
class TexyMultiplier extends Nette\Object
{

    /** @var ITexyFactory[] */
    private $factories = [];

    /** @var Texy\Texy[] */
    private $texy = [];

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
     * @return self
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
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->getTexy(), $name), $args);
    }

}
