# usePHP App Router

Next.js App Router style file-based routing for PHP, built on [usePHP](https://github.com/polidog/use-php).

## Requirements

- PHP >= 8.4
- [polidog/use-php](https://github.com/polidog/use-php) ^0.0.2

## Installation

```bash
composer require polidog/usephp-approuter
```

## Quick Start

```php
<?php
// public/index.php
require_once __DIR__ . '/../vendor/autoload.php';

use Polidog\UsephpApprouter\AppRouter;

$app = AppRouter::create(__DIR__ . '/../src/app');
$app->run();
```

## Directory Structure

```
app/
  layout.php          -> Root layout (wraps all pages)
  page.php            -> /
  error.php           -> Error page (404 etc.)
  about/
    page.php          -> /about
  counter/
    page.php          -> /counter
  todo/
    page.php          -> /todo
  form/
    page.php          -> /form
  blog/
    [slug]/
      page.php        -> /blog/:slug (dynamic route)
```

## Page Types

### Function Page (closure-based)

Pages return a closure that receives a `PageContext`. The closure returns a render function.

```php
<?php
declare(strict_types=1);

use Polidog\UsePhp\Html\H;
use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'About']);

    return function (): Element {
        return H::div(children: 'Hello from About page');
    };
};
```

Helpers stay encapsulated inside the outer closure:

```php
return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'About']);

    $renderCard = function (string $title): Element {
        return H::div(children: H::h3(children: $title));
    };

    return function () use ($renderCard): Element {
        return H::div(children: [$renderCard('Hello')]);
    };
};
```

### Dynamic Routes

Directory names wrapped in brackets (e.g. `[slug]`) become URL parameters, accessible via `$ctx->params`:

```php
// app/blog/[slug]/page.php
return function (PageContext $ctx) {
    $slug = $ctx->params['slug'] ?? '';
    $ctx->metadata(['title' => ucwords($slug) . ' - Blog']);

    return function () use ($slug): Element {
        return H::div(children: "Blog post: {$slug}");
    };
};
```

### useState Hook

```php
use function Polidog\UsePhp\Runtime\useState;

return function (PageContext $ctx) {
    return function (): Element {
        [$count, $setCount] = useState(0);

        return H::div(children: [
            H::span(children: (string) $count),
            H::button(
                onClick: fn() => $setCount($count + 1),
                children: '+',
            ),
        ]);
    };
};
```

### Class Page

Class-based pages are also supported by extending `PageComponent`:

```php
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
        $this->setMetadata(['title' => 'Form']);
        [$data, $setData] = $this->useState([]);
        $action = $this->action([$this, 'handleSubmit']);

        return H::form(action: $action, children: [
            H::input(type: 'text', name: 'name'),
            H::button(type: 'submit', children: 'Send'),
        ]);
    }

    protected function handleSubmit(array $formData): void
    {
        // handle form submission
    }
}
```

## Layouts

Each directory can have a `layout.php` that wraps all pages beneath it. Layouts implement `LayoutInterface`.

## Configuration

```php
$app = AppRouter::create(__DIR__ . '/../src/app');
$app->setJsPath('/assets/app.js');
$app->addCssPath('/assets/style.css');
$app->setContainer($container); // PSR-11 container (optional)
$app->run();
```

## Running Tests

```bash
vendor/bin/phpunit
```

## License

MIT
