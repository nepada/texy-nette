<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Texy;


interface ITexyFactory
{

    /**
     * @return Texy\Texy
     */
    public function create(): Texy\Texy;

}
