<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Nepada;
use Texy;

final class DefaultTexyFactory implements Nepada\Texy\TexyFactory
{

    public function create(): Texy\Texy
    {
        $texy = new Texy\Texy();

        return $texy;
    }

}
