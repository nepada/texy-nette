<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

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
    public function create()
    {
        $texy = new Texy\Texy();
        $texy->setOutputMode(Texy\Texy::HTML5);

        return $texy;
    }

}
