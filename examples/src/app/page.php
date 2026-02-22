<?php

declare(strict_types=1);

namespace App;

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class HomePage extends PageComponent
{
    public function render(): Element
    {
        $this->setMetadata([
            'title' => 'Home - usePHP App Router',
            'description' => 'Next.js App Router style file-based routing for PHP',
        ]);

        [$visitCount, $setVisitCount] = $this->useState(0);
        $setVisitCount($visitCount + 1);

        $cardStyle = 'padding: 20px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-decoration: none; color: inherit; display: block;';

        return H::div(children: [
            H::h1(style: 'margin-bottom: 8px;', children: 'Welcome to usePHP App Router'),
            H::p(style: 'color: #666; margin-bottom: 24px;', children: 'Next.js-style file-based routing for PHP with React Hooks.'),

            H::p(
                style: 'padding: 8px 16px; background: #e8f4fd; border-radius: 6px; display: inline-block; margin-bottom: 24px;',
                children: "Page rendered {$visitCount} time(s) in this session (useState demo)",
            ),

            H::h2(style: 'margin-bottom: 16px;', children: 'Examples'),
            H::div(
                style: 'display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; margin-bottom: 32px;',
                children: [
                    H::a(href: '/counter', style: $cardStyle, children: [
                        H::h3(style: 'margin: 0 0 8px; color: #e74c3c;', children: 'Counter'),
                        H::p(style: 'margin: 0; font-size: 14px; color: #666;', children: 'useState hook with increment / decrement / reset buttons.'),
                    ]),
                    H::a(href: '/todo', style: $cardStyle, children: [
                        H::h3(style: 'margin: 0 0 8px; color: #2ecc71;', children: 'Todo List'),
                        H::p(style: 'margin: 0; font-size: 14px; color: #666;', children: 'useState with array state: toggle done, delete items.'),
                    ]),
                    H::a(href: '/form', style: $cardStyle, children: [
                        H::h3(style: 'margin: 0 0 8px; color: #3498db;', children: 'Form Action'),
                        H::p(style: 'margin: 0; font-size: 14px; color: #666;', children: 'Server-side form handling with auto CSRF protection.'),
                    ]),
                    H::a(href: '/blog/hello-world', style: $cardStyle, children: [
                        H::h3(style: 'margin: 0 0 8px; color: #9b59b6;', children: 'Dynamic Route'),
                        H::p(style: 'margin: 0; font-size: 14px; color: #666;', children: '/blog/[slug] dynamic segment with getParam().'),
                    ]),
                    H::a(href: '/about', style: $cardStyle, children: [
                        H::h3(style: 'margin: 0 0 8px; color: #f39c12;', children: 'About'),
                        H::p(style: 'margin: 0; font-size: 14px; color: #666;', children: 'App Router directory structure overview.'),
                    ]),
                ],
            ),

            H::h2(style: 'margin-bottom: 12px;', children: 'Features'),
            H::div(
                style: 'padding: 16px 24px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);',
                children: [
                    H::p(children: '- File-based routing: app/page.php, app/about/page.php'),
                    H::p(children: '- Dynamic routes: app/blog/[slug]/page.php'),
                    H::p(children: '- Nested layouts: app/layout.php'),
                    H::p(children: '- React Hooks: $this->useState() for state management'),
                    H::p(children: '- Form actions with CSRF: $this->action([$this, "method"])'),
                    H::p(children: '- PSR-11 Container support (optional)'),
                    H::p(children: '- Error pages: app/error.php'),
                ],
            ),
        ]);
    }
}
