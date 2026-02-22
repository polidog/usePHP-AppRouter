<?php

declare(strict_types=1);

namespace App;

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Layout\LayoutComponent;

class RootLayout extends LayoutComponent
{
    public function render(): Element
    {
        $linkStyle = 'color: white; text-decoration: none; padding: 4px 12px; border-radius: 4px;';
        $linkHoverStyle = $linkStyle . ' background: rgba(255,255,255,0.1);';

        return H::div(children: [
            H::nav(
                style: 'padding: 12px 24px; background: #1a1a2e; display: flex; align-items: center; gap: 8px;',
                children: [
                    H::strong(style: 'color: #e94560; margin-right: 16px; font-size: 18px;', children: 'usePHP'),
                    H::a(href: '/', style: $linkStyle, children: 'Home'),
                    H::a(href: '/counter', style: $linkStyle, children: 'Counter'),
                    H::a(href: '/todo', style: $linkStyle, children: 'Todo'),
                    H::a(href: '/form', style: $linkStyle, children: 'Form'),
                    H::a(href: '/blog/hello-world', style: $linkStyle, children: 'Blog'),
                    H::a(href: '/about', style: $linkStyle, children: 'About'),
                ],
            ),
            H::main(
                style: 'padding: 24px; max-width: 800px; margin: 0 auto;',
                children: [$this->getChildren()],
            ),
        ]);
    }
}
