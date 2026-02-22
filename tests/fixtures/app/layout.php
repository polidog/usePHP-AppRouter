<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests\Fixtures;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Layout\LayoutComponent;

class RootLayout extends LayoutComponent
{
    public function render(): Element
    {
        return new Element('div', ['className' => 'root-layout'], [
            $this->getChildren(),
        ]);
    }
}
