<?php

declare(strict_types=1);

require_once './configs/settings.php';

/**
 * @return string
 */
function getRouteFromQuery(): string
{
    $controllersBasePath = 'controllers/';

    $availableRouteNames = array_keys(AVAILABLE_ROUTES);

    if (!isset($_GET['page']) || !in_array($_GET['page'], $availableRouteNames, true)) {
        return realpath($controllersBasePath . DEFAULT_ROUTE);
    }

    return realpath($controllersBasePath . AVAILABLE_ROUTES[$_GET['page']]);
}
