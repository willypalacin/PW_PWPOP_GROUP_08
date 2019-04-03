<?php

require_once __DIR__ . '/../vendor/autoload.php';

$settings = require_once __DIR__ . '/../config/settings.php';

$app = new \Slim\App($settings);

require_once __DIR__ . '/../config/routes.php';

require_once __DIR__ . '/../config/dependencies.php';

$app->run();

