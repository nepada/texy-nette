<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte\Compiler\Tag;
use Latte\Compiler\TemplateParser;
use Latte\ContentType;
use Latte\Extension;
use Latte\Runtime\FilterInfo;
use Nepada\Texy\TexyMultiplier;
use Texy\Helpers;

final class TexyLatteExtension extends Extension
{

    private TexyMultiplier $texyMultiplier;

    public function __construct(TexyMultiplier $texyMultiplier)
    {
        $this->texyMultiplier = $texyMultiplier;
    }

    /**
     * @return array<string, callable(Tag, TemplateParser): (\Generator|void)|\stdClass>
     */
    public function getTags(): array
    {
        return [
            'texy' => fn (Tag $tag, TemplateParser $parser) => yield from TexyNode::create($tag, $parser, $this->processBlock(...)),
            'texyLine' => fn (Tag $tag, TemplateParser $parser) => yield from TexyNode::create($tag, $parser, $this->texyMultiplier->processLine(...)),
        ];
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
            'texy' => fn (string $text, string $tag, ?string $mode = null): string => match ($tag) {
                    'texy' => $this->processBlock($text, $mode),
                    'texyLine' => $this->texyMultiplier->processLine($text, $mode),
                    default => throw new \InvalidArgumentException("Unsupported texy tag '{$tag}'"),
            },
        ];
    }

    public function texyFilter(FilterInfo $filterInfo, string $text, ?string $mode = null): string
    {
        if (! in_array($filterInfo->contentType, [null, ContentType::Text, ContentType::Html], true)) {
            trigger_error('Filter |texy used with incompatible type ' . strtoupper($filterInfo->contentType), E_USER_WARNING);
        }
        $filterInfo->contentType = ContentType::Html;
        return $this->processBlock($text, $mode);
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
        $filterInfo->validate([null, ContentType::Text], 'texyTypo');
        return $this->texyMultiplier->processTypo($text, $mode);
    }

    public function processBlock(string $text, ?string $mode = null): string
    {
        $text = Helpers::outdent(str_replace("\t", '    ', $text));
        return $this->texyMultiplier->processBlock($text, $mode);
    }

}
