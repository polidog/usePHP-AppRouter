<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests\Fixtures;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class HomePage extends PageComponent
{
    public function render(): Element
    {
        return new Element('div', [], ['Home Page']);
    }
}
