<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

declare(strict_types = 1);

namespace Nepada\Texy;

use Nepada;
use Nette;
use Texy;


class TexyFactory implements Nepada\Texy\ITexyFactory
{

    use Nette\SmartObject;


    /**
     * @return Texy\Texy
     */
    public function create(): Texy\Texy
    {
        $texy = new Texy\Texy();
        $texy->setOutputMode(Texy\Texy::HTML5);

        return $texy;
    }

}
