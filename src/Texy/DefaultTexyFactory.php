<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nepada;
use Nette;
use Texy;

class DefaultTexyFactory implements Nepada\Texy\ITexyFactory
{

    use Nette\SmartObject;

    public function create(): Texy\Texy
    {
        $texy = new Texy\Texy();

        return $texy;
    }

}
