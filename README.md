# usePHP App Router

Next.js App Router style file-based routing for PHP, built on [usePHP](https://github.com/polidog/usePHP).

## Requirements

- PHP >= 8.5
- [polidog/use-php](https://github.com/polidog/usePHP) ^0.1.0

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
  page.psx            -> /
  error.php           -> Error page (404 etc.)
  about/
    page.psx          -> /about
  counter/
    page.psx          -> /counter
  todo/
    page.psx          -> /todo
  form/
    page.psx          -> /form
  blog/
    [slug]/
      page.psx        -> /blog/:slug (dynamic route)
```

## Page Types

### Function Page (closure-based)

Pages return a closure that receives a `PageContext`. The closure returns a render function. Use `page.psx` to write the inner render in TSX-like markup.

```php
<?php
// app/about/page.psx
declare(strict_types=1);

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageContext;

return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'About']);

    return function (): Element {
        return (
            <div className="container">
                <h1>About</h1>
                <p>Written in PSX.</p>
            </div>
        );
    };
};
```

Helpers stay encapsulated inside the outer closure:

```php
return function (PageContext $ctx) {
    $ctx->metadata(['title' => 'About']);

    $renderCard = function (string $title): Element {
        return (
            <div>
                <h3>{$title}</h3>
            </div>
        );
    };

    return function () use ($renderCard): Element {
        return (
            <div>
                {$renderCard('Hello')}
            </div>
        );
    };
};
```

### Dynamic Routes

Directory names wrapped in brackets (e.g. `[slug]`) become URL parameters, accessible via `$ctx->params`:

```php
// app/blog/[slug]/page.psx
return function (PageContext $ctx) {
    $slug = $ctx->params['slug'] ?? '';
    $ctx->metadata(['title' => ucwords($slug) . ' - Blog']);

    return function () use ($slug): Element {
        return (
            <div>Blog post: {$slug}</div>
        );
    };
};
```

### useState Hook

```php
use function Polidog\UsePhp\Runtime\useState;

return function (PageContext $ctx) {
    return function (): Element {
        [$count, $setCount] = useState(0);

        return (
            <div>
                <span>{(string) $count}</span>
                <button onClick={fn() => $setCount($count + 1)}>+</button>
            </div>
        );
    };
};
```

### Class Page

Class-based pages are also supported by extending `PageComponent`. Use `page.psx` to write the render method in TSX-like markup:

```php
<?php
// app/form/page.psx
declare(strict_types=1);

namespace App\Form;

use Polidog\UsePhp\Runtime\Element;
use Polidog\UsephpApprouter\Component\PageComponent;

class FormPage extends PageComponent
{
    public function render(): Element
    {
        $this->setMetadata(['title' => 'Form']);
        [$data, $setData] = $this->useState([]);
        $action = $this->action([$this, 'handleSubmit']);

        return (
            <form action={$action}>
                <input type="text" name="name" />
                <button type="submit">Send</button>
            </form>
        );
    }

    protected function handleSubmit(array $formData): void
    {
        // handle form submission
    }
}
```

## Compiling .psx files

`.psx` files must be compiled. Output goes to `var/cache/psx/` by default (sha1-named files plus `manifest.php`); the source tree only ever contains `.psx`.

```bash
# Production / CI: pre-compile once
./vendor/bin/usephp compile src/app
./vendor/bin/usephp compile src/app --check   # CI guard

# Dev loop: watch and recompile on save
./vendor/bin/usephp compile src/app --watch

# Override the cache location
./vendor/bin/usephp compile src/app --cache=build/psx
```

Or let AppRouter compile on demand during development:

```php
// Default cache: <appDir>/../var/cache/psx
$app = AppRouter::create(__DIR__ . '/../src/app', autoCompilePsx: true);

// Or pass an explicit cache directory (must match the CLI if you also
// use `vendor/bin/usephp compile`):
$app = AppRouter::create(
    __DIR__ . '/../src/app',
    autoCompilePsx: true,
    psxCacheDir: __DIR__ . '/../build/psx',
);
```

`page.psx` and `page.php` cannot coexist in the same directory — the scanner errors if both are present.

Add the cache directory to `.gitignore`:

```gitignore
/var/cache/psx/
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
