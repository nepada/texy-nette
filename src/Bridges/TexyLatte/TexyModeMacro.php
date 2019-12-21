<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte;
use Latte\MacroNode;
use Nepada\Texy\TexyMultiplier;
use Nette;

/**
 * Macro {texyMode ...}
 */
final class TexyModeMacro implements Latte\IMacro
{

    use Nette\SmartObject;

    private bool $isUsed = false;

    public static function install(Latte\Compiler $compiler): void
    {
        $me = new static();
        $compiler->addMacro('texyMode', $me);
    }

    /**
     * Initializes before template parsing.
     */
    public function initialize(): void
    {
        $this->isUsed = false;
    }

    /**
     * Finishes template parsing.
     *
     * @return string[] [prolog, epilog]
     */
    public function finalize(): array
    {
        if ($this->isUsed) {
            return [static::class . '::validateTemplate($this);', ''];
        }

        return [];
    }

    /**
     * New node is found. Returns FALSE to reject.
     *
     * @param MacroNode $node
     * @throws Latte\CompileException
     */
    public function nodeOpened(MacroNode $node): void
    {
        if ($node->modifiers !== '') {
            throw new Latte\CompileException("Modifiers are not allowed in {{$node->name}}.");
        }

        $word = $node->tokenizer->fetchWord();
        if ($word === null) {
            throw new Latte\CompileException("Missing mode name in {{$node->name}}.");
        }

        $word = $node->tokenizer->fetchWord();
        if ($word !== null) {
            throw new Latte\CompileException("Multiple arguments are not supported in {{$node->name}}.");
        }

        $this->isUsed = true;
        $node->empty = false;
        $node->tokenizer->reset();
        $node->openingCode = Latte\PhpWriter::using($node)
            ->write('<?php $this->global->texyModeStack[] = $this->global->texy->getMode(); $this->global->texy->setMode(%node.word); ?>');
    }

    /**
     * Node is closed.
     *
     * @param MacroNode $node
     */
    public function nodeClosed(MacroNode $node): void
    {
        $node->closingCode = '<?php $this->global->texy->setMode(array_pop($this->global->texyModeStack)); ?>';
    }

    public static function validateTemplate(Latte\Runtime\Template $template): void
    {
        if (! isset($template->global->texy) || ! $template->global->texy instanceof TexyMultiplier) {
            $where = isset($template->global->control) && $template->global->control instanceof Nette\ComponentModel\IComponent
                ? ' in component ' . get_class($template->global->control) . '(' . $template->global->control->getName() . ')'
                : null;

            throw new \LogicException("TexyMultiplier instance not found{$where}.");
        }
    }

}
