<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Document;

interface DocumentInterface
{
    public function render(string $content): string;

    public function renderError(int $statusCode, string $message): string;
}
