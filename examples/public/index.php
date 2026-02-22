<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Polidog\UsephpApprouter\AppRouter;

$app = AppRouter::create(__DIR__ . '/../src/app');
$app->setJsPath('/usephp.js');
$app->run();
