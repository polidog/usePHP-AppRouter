<?php

declare(strict_types=1);

namespace Polidog\UsephpApprouter\Tests;

use PHPUnit\Framework\TestCase;
use Polidog\UsephpApprouter\AppRouter;

/**
 * End-to-end tests for the .psx page support added in feat/psx-pages.
 *
 * AppRouter has no public test seam for `loadPage`, so we exercise the path
 * through the public surface: build a small fixture app on disk, point
 * AppRouter at it, and observe behaviour by simulating a request.
 */
class PsxIntegrationTest extends TestCase
{
    private string $workDir;

    protected function setUp(): void
    {
        $this->workDir = \sys_get_temp_dir() . '/psx-approuter-' . \uniqid();
        \mkdir($this->workDir, 0o777, true);
    }

    protected function tearDown(): void
    {
        $this->rmrf($this->workDir);
    }

    public function testLoadPageThrowsWhenCompiledPsxMissing(): void
    {
        \file_put_contents(
            $this->workDir . '/page.psx',
            "<?php\nuse Polidog\\UsephpApprouter\\Component\\PageContext;\n"
            . "return fn(PageContext \$ctx) => fn() => 'irrelevant';\n",
        );

        $app = AppRouter::create($this->workDir);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Compiled PSX not found');
        $this->runApp($app, '/');
    }

    public function testAutoCompileGeneratesSiblingAndPageRenders(): void
    {
        \file_put_contents(
            $this->workDir . '/page.psx',
            <<<'PSX'
            <?php
            use Polidog\UsePhp\Html\H;
            use Polidog\UsePhp\Runtime\Element;
            use Polidog\UsephpApprouter\Component\PageContext;

            return function (PageContext $ctx) {
                $ctx->metadata(['title' => 'PSX Home']);
                return function (): Element {
                    return <div><h1>Auto-compiled</h1></div>;
                };
            };
            PSX,
        );

        $app = AppRouter::create($this->workDir, autoCompilePsx: true);

        $output = $this->runApp($app, '/');

        self::assertFileExists($this->workDir . '/page.psx.php');
        self::assertStringContainsString('Auto-compiled', $output);
    }

    public function testPrecompiledPsxIsLoadedWithoutAutoCompile(): void
    {
        // Hand-compile via the same Compiler the CLI uses, to verify the
        // production path (no auto-compile flag) finds the sibling.
        \file_put_contents(
            $this->workDir . '/page.psx',
            <<<'PSX'
            <?php
            use Polidog\UsePhp\Html\H;
            use Polidog\UsePhp\Runtime\Element;
            use Polidog\UsephpApprouter\Component\PageContext;

            return function (PageContext $ctx) {
                return function (): Element {
                    return <p>Pre-compiled output</p>;
                };
            };
            PSX,
        );
        $compiler = new \Polidog\UsePhp\Psx\Compiler();
        $compiled = $compiler->compile(\file_get_contents($this->workDir . '/page.psx'));
        \file_put_contents($this->workDir . '/page.psx.php', $compiled);

        $app = AppRouter::create($this->workDir);

        $output = $this->runApp($app, '/');
        self::assertStringContainsString('Pre-compiled output', $output);
    }

    private function runApp(AppRouter $app, string $path): string
    {
        $_SERVER['REQUEST_URI'] = $path;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        \ob_start();
        try {
            $app->run();
        } finally {
            $output = (string) \ob_get_clean();
        }
        return $output;
    }

    private function rmrf(string $path): void
    {
        if (!\file_exists($path)) {
            return;
        }
        if (\is_file($path) || \is_link($path)) {
            @\unlink($path);
            return;
        }
        $entries = \scandir($path);
        if ($entries === false) {
            return;
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $this->rmrf($path . '/' . $entry);
        }
        @\rmdir($path);
    }
}
