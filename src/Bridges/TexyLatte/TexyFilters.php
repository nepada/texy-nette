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
        if ($singleLine) {
            trigger_error('Using |texy filter with $singleLine = true is deprecated, use |texyLine filter instead', E_USER_DEPRECATED);
        } elseif (func_num_args() > 2) {
            trigger_error('Parameter $singleLine of |texy filter is deprecated', E_USER_DEPRECATED);
        }
        if (! in_array($filterInfo->contentType, [null, Engine::CONTENT_TEXT, Engine::CONTENT_HTML], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }

        $filterInfo->contentType = Engine::CONTENT_HTML;
        if ($singleLine) {
            return $this->texyMultiplier->processLine($text);
        } else {
            return $this->texyMultiplier->processBlock($text);
        }
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
