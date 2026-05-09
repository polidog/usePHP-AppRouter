<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Polidog\UsephpApprouter\AppRouter;

// autoCompilePsx: true compiles .psx pages on demand during development.
// For production, drop the flag and run `vendor/bin/usephp compile examples/src/app`
// as part of your deploy step.
$app = AppRouter::create(__DIR__ . '/../src/app', autoCompilePsx: true);
$app->setJsPath('/usephp.js');
$app->run();
