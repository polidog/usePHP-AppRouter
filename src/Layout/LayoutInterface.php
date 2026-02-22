<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Layout;

use Polidog\UsePhp\Runtime\Element;

interface LayoutInterface
{
    public function render(): Element;

    /**
     * @param Element|array<Element|string>|string $children
     */
    public function setChildren(Element|array|string $children): void;
}
