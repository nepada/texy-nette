<?php
/**
 * This file is part of the nepada/texy-nette.
 * Copyright (c) 2016 Petr Morávek (petr@pada.cz)
 */

namespace Nepada\Texy;

use Texy;


interface ITexyFactory
{

    /**
     * @return Texy\Texy
     */
    public function create();

}
