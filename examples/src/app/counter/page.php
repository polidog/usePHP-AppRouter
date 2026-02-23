<?php

declare(strict_types=1);

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'Counter - usePHP App']);

    $Counter = require __DIR__ . '/Counter.php';

    return function () use ($Counter): Element {
        return H::div(children: [
            H::h1(children: 'Counter Example'),
            H::p(children: 'This demonstrates a function component with the global useState hook.'),

            $Counter(),

            H::div(
                style: 'padding: 16px; background: #f8f9fa; border-radius: 8px; font-family: monospace; font-size: 14px;',
                children: [
                    H::p(style: 'margin: 0 0 8px; color: #666;', children: '// Function component with global useState:'),
                    H::p(style: 'margin: 0;', children: 'function Counter(): Element {'),
                    H::p(style: 'margin: 0; padding-left: 16px;', children: '[$count, $setCount] = useState(0);'),
                    H::p(style: 'margin: 0; padding-left: 16px;', children: 'return H::div(...);'),
                    H::p(style: 'margin: 0;', children: '}'),
                ],
            ),
        ]);
    };
};
