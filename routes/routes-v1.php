<?php

namespace BertMaurau\URLShortener;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Controllers AS Controllers;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// -----------------------------------------------------------------------------
// Start of the routing definitions
// -----------------------------------------------------------------------------
// Index
$route -> map('GET', '/', function(ServerRequestInterface $request, ResponseInterface $response) {

    // You can do whatever you want here, This route is not required or anything.
    return Core\Output::OK($response, date('Y-m-d H:i:s'));
});

/**
 * =============================================================================
 *  Public
 * =============================================================================
 */
// get the external url
$route -> map('GET', '/url', [new Controllers\UrlController, 'index']);
$route -> map('GET', '/{code}', [new Controllers\UrlController, 'index']);

$route -> group('', function ($route) {

    // generate new anonymous url
    $route -> map('POST', '/urls', [new Controllers\UrlController, 'create']);

// only allow from platform
}) -> middleware($leadcampApi);
