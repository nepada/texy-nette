<?php
declare(strict_types = 1);

use Latte\Compiler\Nodes\Php\ArrayItemNode;
use Latte\Compiler\Nodes\Php\Expression\ArrayItemNode as DeprecatedArrayItemNode;

if (class_exists(ArrayItemNode::class) && ! class_exists(DeprecatedArrayItemNode::class)) {
    class_alias(ArrayItemNode::class, DeprecatedArrayItemNode::class);
}
