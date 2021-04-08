<?php

namespace BertMaurau\URLShortener\Middlewares;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Models AS Models;
use BertMaurau\URLShortener\Modules AS Modules;

/**
 * The actual middleware function that gets called upon routing.
 * You can do whatever you'd like here before continuing executing the requested
 * actions etc.
 */
$leadcampApi = function ($request, $response, callable $next) {

    // get the auth-token from the request headers
    $token = Core\Auth::getBearerToken();

//    $remoteAddress = Core\Auth::getRemoteAddress();
//
//    $whitelist = [];
//    if (Core\Config::getInstance() -> API() -> env == 'dev') {
//        $whitelist = ['127.0.0.1', '::1'];
//    } else {
//        $whitelist = ['83.217.78.250'];
//    }
//
//    if (!in_array($remoteAddress, $whitelist)) {
//        return Core\Output::NotAuthorized($response, "Unauthorized origin.");
//    }
    // check if there's even a token present
    if ($token) {

        try {
            // Get the JSON data that has been encoded (can contain whatever you like)
            // and assign that data to the Auth object
            $tokenData = (object) Modules\JWT::decode($token, null, false);

            // check for required token data values
            foreach (['env', 'userId'] as $key => $value) {
                if (!isset($tokenData -> {$value})) {
                    return Core\Output::NotAuthorized($response, "Invalid authToken.");
                }
            }

            // check the environment of the token against the running environment
            if ($tokenData -> env !== Core\Config::getInstance() -> API() -> env) {
                return Core\Output::NotAuthorized($response, "The provided authToken does not match the current Environment.");
            }

            // assign the data to the auth object
            Core\Auth::assign((array) $tokenData);
        } catch (\Exception $ex) {

            // Send a response when the integrity-check of the token failed.
            // This will happen if the passed encrypted data is not a valid JSON-string
            // due to a wrong encryption or bad encoding.
            return Core\Output::NotAuthorized($response, "The authToken integrity check failed!");
        }
    } else {

        // the response when there is no token present
        return Core\Output::NotAuthorized($response, "No authToken provided!");
    }

    // continue the request if the user is allowed (passed the above checks)
    $response = $next($request, $response);

    return $response;
};
