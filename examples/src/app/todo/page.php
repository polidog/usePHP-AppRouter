<?php

declare(strict_types=1);

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

use function Polidog\UsePhp\Runtime\useState;

return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'Todo List - usePHP App']);

    return function (): Element {
        [$todos, $setTodos] = useState([
            ['id' => 1, 'text' => 'Learn usePHP', 'done' => false],
            ['id' => 2, 'text' => 'Build an App Router app', 'done' => false],
            ['id' => 3, 'text' => 'Try useState hook', 'done' => true],
        ]);

        $doneCount = count(array_filter($todos, fn($t) => $t['done']));
        $totalCount = count($todos);

        $items = array_map(
            fn(array $todo): Element => H::li(
                style: 'display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: white; border-radius: 6px; margin-bottom: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);',
                children: [
                    H::button(
                        style: 'width: 28px; height: 28px; border-radius: 50%; border: 2px solid ' . ($todo['done'] ? '#2ecc71' : '#ddd') . '; background: ' . ($todo['done'] ? '#2ecc71' : 'transparent') . '; color: white; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;',
                        onClick: fn() => $setTodos(
                            array_map(
                                fn($t) => $t['id'] === $todo['id']
                                    ? [...$t, 'done' => !$t['done']]
                                    : $t,
                                $todos,
                            ),
                        ),
                        children: $todo['done'] ? 'v' : '',
                    ),
                    H::span(
                        style: $todo['done']
                            ? 'text-decoration: line-through; color: #999; flex: 1;'
                            : 'flex: 1;',
                        children: $todo['text'],
                    ),
                    H::button(
                        style: 'padding: 4px 10px; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;',
                        onClick: fn() => $setTodos(
                            array_values(array_filter(
                                $todos,
                                fn($t) => $t['id'] !== $todo['id'],
                            )),
                        ),
                        children: 'x',
                    ),
                ],
            ),
            $todos,
        );

        return H::div(children: [
            H::h1(children: 'Todo List Example'),
            H::p(children: 'This page demonstrates useState with array state management.'),

            H::div(
                style: 'display: flex; gap: 12px; margin: 16px 0;',
                children: [
                    H::span(
                        style: 'padding: 6px 12px; background: #3498db; color: white; border-radius: 16px; font-size: 14px;',
                        children: "{$doneCount} / {$totalCount} completed",
                    ),
                ],
            ),

            H::ul(
                style: 'list-style: none; padding: 0; max-width: 500px;',
                children: $items,
            ),

            H::div(
                style: 'margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; font-family: monospace; font-size: 14px;',
                children: [
                    H::p(style: 'margin: 0 0 8px; color: #666;', children: '// Array state with useState:'),
                    H::p(style: 'margin: 0;', children: '[$todos, $setTodos] = useState([...]);'),
                    H::p(style: 'margin: 4px 0 0;', children: '// Toggle: $setTodos(array_map(...))'),
                    H::p(style: 'margin: 4px 0 0;', children: '// Delete: $setTodos(array_filter(...))'),
                ],
            ),
        ]);
    };
};
