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
$route -> map('GET', '/c/{code}', [new Controllers\UrlController, 'index']);
$route -> map('GET', '/a/{alias}', [new Controllers\UrlController, 'index']);
$route -> map('POST', '/url', [new Controllers\UrlController, 'index']);
$route -> map('POST', '/c/{code}', [new Controllers\UrlController, 'index']);
$route -> map('POST', '/a/{alias}', [new Controllers\UrlController, 'index']);

$route -> map('POST', '/url-request', [new Controllers\UrlRequestController, 'browserDetect']);

// generate new anonymous url
$route -> map('POST', '/urls', [new Controllers\UrlController, 'create']);

// Validate the given Login
$route -> map('POST', '/validate-login', [new Controllers\UserController, 'login']);
$route -> map('POST', '/register', [new Controllers\UserController, 'create']);


/**
 * =============================================================================
 *  Private
 * =============================================================================
 */
$route -> group('', function ($route) {

    $route -> map('POST', '/sign-out', [new Controllers\UserController, 'logout']);

    /**
     * -------------------------------------------------------------------------
     *  Auth-user
     * -------------------------------------------------------------------------
     */
    $route -> map('GET', '/me', [new Controllers\UserController, 'index']);
    $route -> map('PATCH', '/me', [new Controllers\UserController, 'update']);
    $route -> map('DELETE', '/me', [new Controllers\UserController, 'delete']);

    /**
     * -------------------------------------------------------------------------
     *  Auth-user URLs
     * -------------------------------------------------------------------------
     */
    $route -> map('GET', '/my/urls', [new Controllers\UserUrlController, 'index']);
    $route -> map('GET', '/my/urls/{userUrlId}', [new Controllers\UserUrlController, 'show']);

    $route -> map('GET', '/my/urls/{userUrlId}/overview', [new Controllers\UserUrlController, 'overview']);

    $route -> map('POST', '/my/urls', [new Controllers\UserUrlController, 'create']);
    $route -> map('POST', '/my/urls/{userUrlId}/aliasses', [new Controllers\UserUrlAliasController, 'create']);

    $route -> map('DELETE', '/my/urls/{userUrlId}', [new Controllers\UserUrlController, 'delete']);
    $route -> map('DELETE', '/my/urls/{userUrlId}/aliasses/{aliasId}', [new Controllers\UserUrlAliasController, 'delete']);

    $route -> map('PATCH', '/my/urls/{userUrlId}', [new Controllers\UserUrlController, 'update']);
    $route -> map('PATCH', '/my/urls/{userUrlId}/aliasses/{aliasId}', [new Controllers\UserUrlAliasController, 'update']);


    // end
}) -> middleware($authentication);
