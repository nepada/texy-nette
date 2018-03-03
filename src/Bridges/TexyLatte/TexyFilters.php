<?php
declare(strict_types = 1);

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


    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    public function process(FilterInfo $filterInfo, string $text, bool $singleLine = false): string
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = ($this->texyMultiplier->getOutputMode() & Texy::XML) ? Engine::CONTENT_XHTML : Engine::CONTENT_HTML;
        return $this->texyMultiplier->process($text, $singleLine);
    }

    public function processLine(FilterInfo $filterInfo, string $text): string
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texyLine used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = ($this->texyMultiplier->getOutputMode() & Texy::XML) ? Engine::CONTENT_XHTML : Engine::CONTENT_HTML;
        return $this->texyMultiplier->processLine($text);
    }

    public function processTypo(FilterInfo $filterInfo, string $text): string
    {
        if (!in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML, Engine::CONTENT_XHTML, Engine::CONTENT_XML], true)) {
            trigger_error('Filter |texyTypo used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        return $this->texyMultiplier->processTypo($text);
    }

}
