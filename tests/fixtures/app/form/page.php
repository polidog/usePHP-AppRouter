<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests\Fixtures\Form;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class FormPage extends PageComponent
{
    public function render(): Element
    {
        return new Element('div', [], ['Form Page']);
    }
}
