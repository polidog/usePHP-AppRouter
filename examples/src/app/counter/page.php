<?php

declare(strict_types=1);

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

use function Polidog\UsePhp\Runtime\useState;

return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'Counter - usePHP App']);

    $Counter = function (): Element {
        [$count, $setCount] = useState(0);

        return H::div(
            style: 'display: flex; align-items: center; gap: 16px; margin: 24px 0; padding: 24px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);',
            children: [
                H::button(
                    style: 'padding: 8px 20px; font-size: 20px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer;',
                    onClick: fn() => $setCount($count - 1),
                    children: '-',
                ),
                H::span(
                    style: 'font-size: 48px; font-weight: bold; min-width: 80px; text-align: center;',
                    children: (string) $count,
                ),
                H::button(
                    style: 'padding: 8px 20px; font-size: 20px; background: #2ecc71; color: white; border: none; border-radius: 4px; cursor: pointer;',
                    onClick: fn() => $setCount($count + 1),
                    children: '+',
                ),
                H::button(
                    style: 'padding: 8px 20px; font-size: 14px; background: #95a5a6; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 16px;',
                    onClick: fn() => $setCount(0),
                    children: 'Reset',
                ),
            ],
        );
    };

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
