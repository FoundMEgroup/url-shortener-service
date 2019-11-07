<?php

namespace BertMaurau\URLShortener\Core;

use BertMaurau\URLShortener\Models AS Models;

/**
 * Description of Auth
 *
 * Handles everything concerning the Authentication and user sessions etc.
 *
 * @author Bert Maurau
 */
class Auth
{

    // Put auth items here for easier access (JWT token data values)
    // Don't forget to add the necessary getter/setter.
    private static $userId;

    /**
     * Assign the Auth values
     *
     * @param array $properties The array with the properties to map to this Auth session.
     *
     * @returns void
     */
    public static function assign(array $properties = [])
    {
        // loop properties and attempt to call the setter
        foreach ($properties as $key => $value) {

            // construct the setter name
            $setter = 'set' . ucfirst($key);

            // check if the setter exists and if it is callable
            if (is_callable(array(self, $setter))) {
                // execute the setter
                call_user_func(array(self, $setter), $value);
            }
        }
    }

    /**
     * Hash the given value with the provided salt type
     *
     * @param string $value The value to hash
     * @param string $type The salt to use for the hash
     *
     * @return string The hashed value
     *
     * @throws \Exception
     */
    public static function getHash($value, $type = 'password')
    {
        // check for given value
        if (!$value) {
            return null;
        }

        // check if the requested salt is configured
        if (!in_array($type, ['password', 'token'])) {
            throw new \Exception('No salt found for type `' . $type . '`.');
        }

        // return the hashed string
        return (string) hash('sha256', $value . Config::getInstance() -> Salts() -> {$type});
    }

    /**
     * Check for and get the Bearer token from the Authorization header
     *
     * @return string The extracted token
     */
    public static function getBearerToken()
    {

        $token = null;

        // get the headers first.
        $authorizationHeaderValue = self::getAuthorizationHeaderValue();

        if (!empty($authorizationHeaderValue)) {

            if (preg_match('/Bearer\s(\S+)/', $authorizationHeaderValue, $matches)) {

                // return the token
                $token = $matches[1];
            }
        } else {

            // check if it is allowed to use the token as a GET parameter
            if (Config::getInstance() -> Token() -> urlGetParameterAllowed) {

                // get the token via the configured GET parameter
                $token = filter_input(INPUT_GET, Config::getInstance() -> Token() -> urlGetParameter);
            }
        }

        return $token;
    }

    /**
     * Find the provided token for given User
     *
     * @param string $tokenUid Token UID
     * @param int $userId User ID
     *
     * @return Models\UserAuthToken
     */
    public static function findTokenForUserId(string $tokenUid, int $userId): Models\UserAuthToken
    {
        return (new Models\UserAuthToken) -> findBy(['uid' => $tokenUid, 'user_id' => $userId], $take = 1);
    }

    /**
     * Get the Authorization header from the request Headers
     *
     * @return string The value of the Authorization header
     */
    public static function getAuthorizationHeaderValue()
    {
        $authorizationHeaderValue = null;

        if (!$authorizationHeaderValue = trim(filter_input(INPUT_SERVER, 'Authorization'))) {

            if (!$authorizationHeaderValue = trim(filter_input(INPUT_SERVER, 'HTTP_AUTHORIZATION'))) { // Nginx or fast CGI
                if (function_exists('apache_request_headers')) {

                    $apacheRequestHeaders = apache_request_headers();

                    // Server-side fix for bug in old Android versions (a nice side-effect of
                    // this fix means we don't care about capitalization for Authorization)
                    $requestHeaders = array_combine(array_map('ucwords', array_keys($apacheRequestHeaders)), array_values($apacheRequestHeaders));

                    if (isset($requestHeaders['Authorization'])) {
                        $authorizationHeaderValue = trim($requestHeaders['Authorization']);
                    }
                }
            }
        }

        return $authorizationHeaderValue;
    }

    /**
     * Get the remote address of the client
     *
     * @return string The remote address
     */
    public static function getRemoteAddress()
    {
        $ipaddress = '';

        if (filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } else if (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } else if (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED');
        } else if (filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR');
        } else if (filter_input(INPUT_SERVER, 'HTTP_FORWARDED')) {
            $ipaddress = filter_input(INPUT_SERVER, 'HTTP_FORWARDED');
        } else if (filter_input(INPUT_SERVER, 'REMOTE_ADDR')) {
            $ipaddress = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * Set the UserID
     *
     * @param integer $userId The User ID
     */
    private static function setUserId(int $userId)
    {
        self::$userId = $userId;
    }

    /**
     * Get the User ID
     * @return integer The User ID
     */
    public static function getUserId()
    {
        return self::$userId;
    }

}
