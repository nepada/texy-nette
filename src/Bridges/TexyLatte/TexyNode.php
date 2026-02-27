<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte\Compiler\Nodes\AreaNode;
use Latte\Compiler\Nodes\Php\ArrayItemNode;
use Latte\Compiler\Nodes\Php\Scalar\StringNode;
use Latte\Compiler\Nodes\TextNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;
use Latte\Compiler\TemplateParser;
use Texy;

final class TexyNode extends Texy\Bridges\Latte\TexyNode
{

    private string $tagName;

    /**
     * @param \Closure(string, ?string): string $processor
     * @return \Generator<int, ?array{list<string>}, array{AreaNode, ?Tag}, static|TextNode>
     */
    public static function create(Tag $tag, TemplateParser $parser, \Closure $processor): \Generator
    {
        $parent = parent::create($tag, $parser, $processor);
        $parent->current();
        $parent->send(yield); // phpcs:ignore
        $node = $parent->getReturn();
        if ($node instanceof self) {
            $node->tagName = $tag->name;
        }
        return $node;
    }

    public function print(PrintContext $context): string
    {
        try {
            array_unshift($this->args->items, new ArrayItemNode(new StringNode($this->tagName)));
            return parent::print($context);
        } finally {
            array_shift($this->args->items);
        }
    }

}
