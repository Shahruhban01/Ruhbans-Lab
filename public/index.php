<?php

declare(strict_types=1);

$basePath = dirname(__DIR__);

require_once $basePath . '/app/Core/Autoloader.php';

$autoloader = new \App\Core\Autoloader($basePath);
$autoloader->register();

require_once $basePath . '/app/Helpers/functions.php';

$application = new \App\Core\Application($basePath);
$application->boot();

echo $application->run();
