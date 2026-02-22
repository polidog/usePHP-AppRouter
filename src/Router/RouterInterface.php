<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Router;

interface RouterInterface
{
    public function match(string $path): ?RouteMatch;

    public function getErrorPagePath(): ?string;
}
