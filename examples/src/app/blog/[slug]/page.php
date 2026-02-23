<?php

declare(strict_types=1);

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

use function Polidog\UsePhp\Runtime\useState;

return function (PageContext $ctx) {
    $slug = $ctx->params['slug'] ?? 'unknown';
    $title = ucwords(str_replace('-', ' ', $slug));

    $ctx->metadata(['title' => "{$title} - Blog"]);

    /**
     * @return array<Element>
     */
    $getContent = function (string $slug): array {
        $contents = [
            'hello-world' => [
                H::p(children: 'Welcome to usePHP! This is your first blog post.'),
                H::p(children: 'usePHP brings React-like development patterns to PHP, allowing you to build modern server-rendered applications with familiar concepts like components and hooks.'),
            ],
            'getting-started' => [
                H::p(children: 'Getting started with usePHP is easy. First, install it via Composer:'),
                H::p(
                    style: 'font-family: monospace; background: #f0f0f0; padding: 12px; border-radius: 4px;',
                    children: 'composer require polidog/usephp-approuter',
                ),
                H::p(children: 'Then create your app directory structure and start building!'),
            ],
        ];

        return $contents[$slug] ?? [
            H::p(children: "This is a dynamically generated blog post for: {$slug}"),
            H::p(children: 'The URL parameter [slug] is automatically extracted and available via $params[\'slug\'].'),
            H::p(children: 'Try visiting /blog/hello-world or /blog/getting-started for more content.'),
        ];
    };

    return function () use ($slug, $title, $getContent): Element {

        [$liked, $setLiked] = useState(false);
        [$likeCount, $setLikeCount] = useState(0);

        $content = $getContent($slug);

        return H::div(children: [
            H::div(
                style: 'margin-bottom: 16px;',
                children: [
                    H::a(href: '/', style: 'color: #3498db; text-decoration: none;', children: '<- Back to Home'),
                ],
            ),
            H::h1(children: $title),
            H::div(
                style: 'color: #666; margin-bottom: 16px; font-size: 14px;',
                children: [
                    H::span(children: 'Slug: '),
                    H::span(
                        style: 'font-family: monospace; background: #f0f0f0; padding: 2px 8px; border-radius: 4px;',
                        children: $slug,
                    ),
                ],
            ),

            H::div(style: 'line-height: 1.8; margin-bottom: 24px;', children: $content),

            H::div(
                style: 'display: flex; align-items: center; gap: 12px; padding: 16px; background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);',
                children: [
                    H::button(
                        style: 'padding: 8px 20px; border: 2px solid ' . ($liked ? '#e74c3c' : '#ddd') . '; background: ' . ($liked ? '#ffeaea' : 'white') . '; border-radius: 20px; cursor: pointer; font-size: 14px;',
                        onClick: function () use ($liked, $setLiked, $likeCount, $setLikeCount) {
                            $setLiked(!$liked);
                            return $setLikeCount($liked ? $likeCount - 1 : $likeCount + 1);
                        },
                        children: ($liked ? 'Liked!' : 'Like') . " ({$likeCount})",
                    ),
                    H::span(style: 'color: #666; font-size: 14px;', children: 'Try clicking the like button (useState demo)'),
                ],
            ),

            H::div(
                style: 'margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; font-family: monospace; font-size: 14px;',
                children: [
                    H::p(style: 'margin: 0 0 8px; color: #666;', children: '// Dynamic route + useState:'),
                    H::p(style: 'margin: 0;', children: '$slug = $params[\'slug\'];'),
                    H::p(style: 'margin: 4px 0 0;', children: '[$liked, $setLiked] = useState(false);'),
                ],
            ),
        ]);
    };
};
