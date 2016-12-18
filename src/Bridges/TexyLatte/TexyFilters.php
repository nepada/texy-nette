<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Bridges\TexyLatte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;
use Nepada\Texy\TexyMultiplier;
use Nette;
use Texy\Texy;


class TexyFilters
{

    use Nette\SmartObject;

    /** @var TexyMultiplier */
    private $texyMultiplier;


    /**
     * @param TexyMultiplier $texyMultiplier
     */
    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    /**
     * @param FilterInfo $filterInfo
     * @param string $text
     * @param bool $singleLine
     * @return string
     */
    public function process(FilterInfo $filterInfo, $text, $singleLine = false)
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = ($this->texyMultiplier->getOutputMode() & Texy::XML) ? Engine::CONTENT_XHTML : Engine::CONTENT_HTML;
        return $this->texyMultiplier->process($text, $singleLine);
    }

    /**
     * @param FilterInfo $filterInfo
     * @param string $text
     * @return string
     */
    public function processLine(FilterInfo $filterInfo, $text)
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texyLine used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = ($this->texyMultiplier->getOutputMode() & Texy::XML) ? Engine::CONTENT_XHTML : Engine::CONTENT_HTML;
        return $this->texyMultiplier->processLine($text);
    }

    /**
     * @param FilterInfo $filterInfo
     * @param string $text
     * @return string
     */
    public function processTypo(FilterInfo $filterInfo, $text)
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texyTypo used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        return $this->texyMultiplier->processTypo($text);
    }

}
