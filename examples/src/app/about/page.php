<?php

declare(strict_types=1);

namespace App\About;

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class AboutPage extends PageComponent
{
    public function render(): Element
    {
        $this->setMetadata([
            'title' => 'About - usePHP App',
        ]);

        $codeStyle = 'background: #f0f0f0; padding: 16px; border-radius: 6px; font-family: monospace; font-size: 14px; line-height: 1.8;';

        return H::div(children: [
            H::h1(children: 'About usePHP App Router'),
            H::p(children: 'usePHP is a React Hooks-style PHP framework. The App Router provides Next.js-style file-based routing.'),

            H::h2(style: 'margin-top: 24px;', children: 'Directory Structure'),
            H::div(style: $codeStyle, children: [
                H::p(style: 'margin: 0;', children: 'app/'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'layout.php         -> Root layout'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'page.php           -> /'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'error.php          -> Error page'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'about/'),
                H::p(style: 'margin: 0; padding-left: 3em;', children: 'page.php         -> /about'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'counter/'),
                H::p(style: 'margin: 0; padding-left: 3em;', children: 'page.php         -> /counter (useState demo)'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'todo/'),
                H::p(style: 'margin: 0; padding-left: 3em;', children: 'page.php         -> /todo (useState with arrays)'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'form/'),
                H::p(style: 'margin: 0; padding-left: 3em;', children: 'page.php         -> /form (form action + CSRF)'),
                H::p(style: 'margin: 0; padding-left: 1.5em;', children: 'blog/'),
                H::p(style: 'margin: 0; padding-left: 3em;', children: '[slug]/'),
                H::p(style: 'margin: 0; padding-left: 4.5em;', children: 'page.php       -> /blog/:slug (dynamic)'),
            ]),

            H::h2(style: 'margin-top: 24px;', children: 'Key Concepts'),
            H::div(style: 'display: grid; gap: 16px; margin-top: 12px;', children: [
                $this->renderConcept('useState Hook', '$this->useState($initial) returns [$state, $setState]. State persists in the session.'),
                $this->renderConcept('Form Actions', '$this->action([$this, "methodName"]) generates a secure action token with CSRF protection.'),
                $this->renderConcept('Dynamic Routes', 'Directories like [slug] become URL parameters accessible via $this->getParam("slug").'),
                $this->renderConcept('Nested Layouts', 'Each directory can have a layout.php that wraps all pages beneath it.'),
                $this->renderConcept('PSR-11 Container', 'AppRouter accepts an optional ContainerInterface for dependency injection.'),
            ]),
        ]);
    }

    private function renderConcept(string $title, string $description): Element
    {
        return H::div(
            style: 'padding: 16px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);',
            children: [
                H::h3(style: 'margin: 0 0 6px;', children: $title),
                H::p(style: 'margin: 0; color: #666; font-size: 14px;', children: $description),
            ],
        );
    }
}
