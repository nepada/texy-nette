<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte;
use Latte\MacroNode;
use Nepada\Texy\TexyMultiplier;
use Nette;

/**
 * Macro {texyMode ...}
 *
 * @deprecated specify custom mode explicitly in Latte filter call
 */
final class TexyModeMacro implements Latte\Macro
{

    use Nette\SmartObject;

    private bool $isUsed = false;

    public static function install(Latte\Compiler $compiler): void
    {
        $me = new static();
        $compiler->addMacro('texyMode', $me);
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
     * @return array{string, string}|null (prolog, epilog)
     */
    public function finalize(): ?array
    {
        if ($this->isUsed) {
            return [static::class . '::validateTemplate($this);', ''];
        }

        return null;
    }

    /**
     * New node is found. Returns FALSE to reject.
     *
     * @param MacroNode $node
     * @return bool|null
     * @throws Latte\CompileException
     */
    public function nodeOpened(MacroNode $node): ?bool
    {
        trigger_error('{texyMode} macro is deprecated, specify custom mode explicitly in Latte filter call', E_USER_DEPRECATED);
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
            ->write('<?php $this->global->texyModeStack[] = @$this->global->texy->getMode(); @$this->global->texy->setMode(%node.word); ?>');
        return null;
    }

    /**
     * Node is closed.
     *
     * @param MacroNode $node
     */
    public function nodeClosed(MacroNode $node): void
    {
        $node->closingCode = '<?php @$this->global->texy->setMode(array_pop($this->global->texyModeStack)); ?>';
    }

}
