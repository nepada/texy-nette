<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte\ContentType;
use Latte\Extension;
use Latte\Runtime\FilterInfo;
use Nepada\Texy\TexyMultiplier;

final class TexyLatteExtension extends Extension
{

    private TexyMultiplier $texyMultiplier;

    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    /**
     * @return array<string, callable>
     */
    public function getFilters(): array
    {
        return [
            'texy' => [$this, 'texyFilter'],
            'texyLine' => [$this, 'texyLineFilter'],
            'texyTypo' => [$this, 'texyTypoFilter'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getProviders(): array
    {
        return [
            'texy' => $this->texyMultiplier,
        ];
    }

    public function texyFilter(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, ContentType::Text, ContentType::Html], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }
        $filterInfo->contentType = ContentType::Html;
        return $this->texyMultiplier->processBlock($text, $mode);
    }

    public function texyLineFilter(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, ContentType::Text, ContentType::Html], true)) {
            trigger_error('Filter |texyLine used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }
        $filterInfo->contentType = ContentType::Html;
        return $this->texyMultiplier->processLine($text, $mode);
    }

    public function texyTypoFilter(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, ContentType::Text, ContentType::Html], true)) {
            trigger_error('Filter |texyTypo used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }
        return $this->texyMultiplier->processTypo($text, $mode);
    }

}
