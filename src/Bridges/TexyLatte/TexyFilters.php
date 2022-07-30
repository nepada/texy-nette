<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte\Engine;
use Latte\Runtime\FilterInfo;
use Nepada\Texy\TexyMultiplier;

final class TexyFilters
{

    private TexyMultiplier $texyMultiplier;

    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    public function process(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = Engine::CONTENT_HTML;
        return $this->texyMultiplier->processBlock($text, $mode);
    }

    public function processLine(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texyLine used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = Engine::CONTENT_HTML;
        return $this->texyMultiplier->processLine($text, $mode);
    }

    public function processTypo(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texyTypo used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        return $this->texyMultiplier->processTypo($text, $mode);
    }

}
