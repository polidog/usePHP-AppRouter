<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests\Fixtures\Blog;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class BlogDetailPage extends PageComponent
{
    public function render(): Element
    {
        $slug = $this->getParam('slug') ?? 'unknown';
        return new Element('div', [], ['Blog: ' . $slug]);
    }
}
