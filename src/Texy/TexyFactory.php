<?php
declare(strict_types = 1);

namespace Nepada\Texy;

use Texy;

interface TexyFactory
{

    public function create(): Texy\Texy;

}
