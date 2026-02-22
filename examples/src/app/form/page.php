<?php

declare(strict_types=1);

namespace App\Form;

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class FormPage extends PageComponent
{
    public function render(): Element
    {
        $this->setMetadata([
            'title' => 'Form - usePHP App',
        ]);

        [$messages, $setMessages] = $this->useState([]);
        $saved = $this->getQuery('saved') === '1';
        $action = $this->action([$this, 'handleSubmit']);

        $messageItems = array_map(
            fn(array $msg): Element => H::li(
                style: 'padding: 12px; background: white; border-radius: 6px; margin-bottom: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);',
                children: [
                    H::strong(children: htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8')),
                    H::span(style: 'color: #666;', children: ' (' . htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') . ')'),
                    H::p(style: 'margin: 8px 0 0;', children: htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')),
                ],
            ),
            $messages,
        );

        return H::div(children: [
            H::h1(children: 'Form Action Example'),
            H::p(children: 'This page demonstrates server-side form handling with CSRF protection.'),
            H::p(children: 'Action tokens and CSRF tokens are injected automatically into the form.'),

            $saved
                ? H::div(
                    style: 'padding: 12px 16px; background: #d4edda; color: #155724; border-radius: 6px; margin: 16px 0;',
                    children: 'Message saved successfully!',
                )
                : H::span(children: ''),

            H::form(
                method: 'post',
                action: $action,
                style: 'max-width: 480px; margin: 24px 0; display: grid; gap: 16px;',
                children: [
                    H::div(children: [
                        H::label(style: 'display: block; margin-bottom: 4px; font-weight: bold;', children: 'Name'),
                        H::input(
                            type: 'text',
                            name: 'name',
                            required: true,
                            placeholder: 'Your name',
                            style: 'display: block; width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;',
                        ),
                    ]),
                    H::div(children: [
                        H::label(style: 'display: block; margin-bottom: 4px; font-weight: bold;', children: 'Email'),
                        H::input(
                            type: 'email',
                            name: 'email',
                            required: true,
                            placeholder: 'your@email.com',
                            style: 'display: block; width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;',
                        ),
                    ]),
                    H::div(children: [
                        H::label(style: 'display: block; margin-bottom: 4px; font-weight: bold;', children: 'Message'),
                        H::textarea(
                            name: 'message',
                            rows: 4,
                            required: true,
                            placeholder: 'Write your message...',
                            style: 'display: block; width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; resize: vertical;',
                        ),
                    ]),
                    H::button(
                        type: 'submit',
                        style: 'padding: 12px 24px; background: #333; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;',
                        children: 'Send Message',
                    ),
                ],
            ),

            $messageItems !== []
                ? H::div(children: [
                    H::h2(children: 'Submitted Messages'),
                    H::ul(style: 'list-style: none; padding: 0; max-width: 480px;', children: $messageItems),
                ])
                : H::span(children: ''),

            H::div(
                style: 'margin-top: 24px; padding: 16px; background: #f8f9fa; border-radius: 8px; font-family: monospace; font-size: 14px;',
                children: [
                    H::p(style: 'margin: 0 0 8px; color: #666;', children: '// Form action usage:'),
                    H::p(style: 'margin: 0;', children: '$action = $this->action([$this, \'handleSubmit\']);'),
                    H::p(style: 'margin: 4px 0 0;', children: 'H::form(action: $action, children: [...])'),
                    H::p(style: 'margin: 8px 0 0; color: #666;', children: '// Hidden fields _usephp_action & _usephp_csrf are auto-injected'),
                ],
            ),
        ]);
    }

    /**
     * @param array<string, mixed> $formData
     */
    protected function handleSubmit(array $formData): void
    {
        $name = trim((string) ($formData['name'] ?? ''));
        $email = trim((string) ($formData['email'] ?? ''));
        $message = trim((string) ($formData['message'] ?? ''));

        if ($name === '' || $email === '' || $message === '') {
            return;
        }

        header('Location: /form?saved=1');
        exit;
    }
}
