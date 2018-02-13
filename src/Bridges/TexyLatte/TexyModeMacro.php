<?php
declare(strict_types = 1);

namespace Nepada\Bridges\TexyLatte;

use Latte;
use Latte\MacroNode;
use Nepada;
use Nepada\Texy\TexyMultiplier;
use Nette;


/**
 * Macro {texyMode ...}
 */
class TexyModeMacro implements Latte\IMacro
{

    use Nette\SmartObject;

    /** @var bool */
    private $isUsed;


    /**
     * @param Latte\Compiler $compiler
     */
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
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @return string[] [prolog, epilog]
     */
    public function finalize()
    {
        if ($this->isUsed) {
            return [static::class . '::validateTemplate($this);', ''];
        }

        return [];
    }

    /**
     * New node is found. Returns FALSE to reject.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
     * @param MacroNode $node
     * @return void
     * @throws Latte\CompileException
     */
    public function nodeOpened(MacroNode $node)
    {
        if ($node->modifiers != '') { // intentionally !=
            throw new Latte\CompileException("Modifiers are not allowed in {{$node->name}}.");
        }

        /** @var string|null|false $word */
        $word = $node->tokenizer->fetchWord();
        if ($word === false) {
            throw new Latte\CompileException("Missing mode name in {{$node->name}}.");
        }

        /** @var string|null|false $word */
        $word = $node->tokenizer->fetchWord();
        if ($word !== false) {
            throw new Latte\CompileException("Multiple arguments are not supported in {{$node->name}}.");
        }

        $this->isUsed = true;
        $node->isEmpty = false;
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

    /**
     * @param Latte\Runtime\Template $template
     * @throws Nepada\Texy\InvalidStateException
     */
    public static function validateTemplate(Latte\Runtime\Template $template): void
    {
        if (!isset($template->global->texy) || !$template->global->texy instanceof TexyMultiplier) {
            $where = isset($template->global->control) && $template->global->control instanceof Nette\ComponentModel\IComponent
                ? ' in component ' . get_class($template->global->control) . '(' . $template->global->control->getName() . ')'
                : null;

            throw new Nepada\Texy\InvalidStateException("TexyMultiplier instance not found{$where}.");
        }
    }

}
