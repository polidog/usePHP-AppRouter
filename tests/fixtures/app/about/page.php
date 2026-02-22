<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests\Fixtures\About;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class AboutPage extends PageComponent
{
    public function render(): Element
    {
        return new Element('div', [], ['About Page']);
    }
}
