<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;
use Nepada\Texy\TexyMultiplier;
use Nette;

class TexyFilters
{

    use Nette\SmartObject;

    private TexyMultiplier $texyMultiplier;

    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    public function process(FilterInfo $filterInfo, string $text, bool $singleLine = false): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = Engine::CONTENT_HTML;
        return $this->texyMultiplier->process($text, $singleLine);
    }

    public function processLine(FilterInfo $filterInfo, string $text): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texyLine used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = Engine::CONTENT_HTML;
        return $this->texyMultiplier->processLine($text);
    }

    public function processTypo(FilterInfo $filterInfo, string $text): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texyTypo used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        return $this->texyMultiplier->processTypo($text);
    }

}
