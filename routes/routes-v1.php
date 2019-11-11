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
$route -> map('POST', '/url', [new Controllers\UrlController, 'index']);

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
    $route -> map('POST', '/my/urls', [new Controllers\UserUrlController, 'create']);
    $route -> map('PATCH', '/my/urls/{userUrlId}', [new Controllers\UserUrlController, 'update']);
    $route -> map('DELTE', '/my/urls/{userUrlId}', [new Controllers\UserUrlController, 'delete']);

    $route -> map('GET', '/my/urls/{userUrlId}/analytics', [new Controllers\UserUrlController, 'analytics']);


    /**
     * -------------------------------------------------------------------------
     *  Auth-user URL aliases
     * -------------------------------------------------------------------------
     */
    $route -> map('GET', '/my/urls/{userUrlId}/aliasses', [new Controllers\UserUrlAliasController, 'index']);
    $route -> map('GET', '/my/urls/{userUrlId}/aliasses/{urlAliasId}', [new Controllers\UserUrlAliasController, 'show']);
    $route -> map('POST', '/my/urls/{userUrlId}/aliasses', [new Controllers\UserUrlAliasController, 'create']);
    $route -> map('PATCH', '/my/urls/{userUrlId}/aliasses/{urlAliasId}', [new Controllers\UserUrlAliasController, 'update']);
    $route -> map('DELTE', '/my/urls/{userUrlId}/aliasses/{urlAliasId}', [new Controllers\UserUrlAliasController, 'delete']);

    $route -> map('GET', '/my/urls/{userUrlId}/aliasses/{urlAliasId}/analytics', [new Controllers\UserUrlAliasController, 'analytics']);


    // end
}) -> middleware($authentication);
