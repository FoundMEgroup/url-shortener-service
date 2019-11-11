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
    // redirect to app index
    header("Location: " . Core\Config::getInstance() -> app() -> base_url . Core\Config::getInstance() -> APP() -> root);
    exit;
    // return Core\Output::OK($response, date('Y-m-d H:i:s'));
});

/**
 * =============================================================================
 *  Public
 * =============================================================================
 */
// get the URL (by the short code)
$route -> map('GET', '/c/{urlCode}', [new Controllers\UrlController, 'getByUrlCode']);
$route -> map('POST', '/c/{urlCode}', [new Controllers\UrlController, 'getByUrlCode']);

// get the URL (by the alias code)
$route -> map('GET', '/a/{urlAlias}', [new Controllers\UrlController, 'getByUrlAlias']);
$route -> map('POST', '/a/{urlAlias}', [new Controllers\UrlController, 'getByUrlAlias']);

// generate new anonymous url
$route -> map('POST', '/urls', [new Controllers\UrlController, 'create']);

// Validate the given Login
$route -> map('POST', '/validate-login', [new Controllers\UserController, 'show']);
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
    $route -> map('GET', '/my/urls/{urlCode}', [new Controllers\UserUrlController, 'show']);
    $route -> map('POST', '/my/urls', [new Controllers\UserUrlController, 'create']);
    $route -> map('PATCH', '/my/urls/{urlCode}', [new Controllers\UserUrlController, 'update']);
    $route -> map('DELTE', '/my/urls/{urlCode}', [new Controllers\UserUrlController, 'delete']);

    $route -> map('GET', '/my/urls/{urlCode}/analytics', [new Controllers\UserUrlController, 'analytics']);


    /**
     * -------------------------------------------------------------------------
     *  Auth-user URL aliases
     * -------------------------------------------------------------------------
     */
    $route -> map('GET', '/my/urls/{urlCode}/aliasses', [new Controllers\UserUrlAliasController, 'index']);
    $route -> map('GET', '/my/urls/{urlCode}/aliasses/{urlAlias}', [new Controllers\UserUrlAliasController, 'show']);
    $route -> map('POST', '/my/urls/{urlCode}/aliasses', [new Controllers\UserUrlAliasController, 'create']);
    $route -> map('PATCH', '/my/urls/{urlCode}/aliasses/{urlAlias}', [new Controllers\UserUrlAliasController, 'update']);
    $route -> map('DELTE', '/my/urls/{urlCode}/aliasses/{urlAlias}', [new Controllers\UserUrlAliasController, 'delete']);

    $route -> map('GET', '/my/urls/{urlCode}/aliasses/{urlAlias}/analytics', [new Controllers\UserUrlAliasController, 'analytics']);


    // end
}) -> middleware($authentication);
