<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core as Core;

/**
 * Description of LogRequest
 *
 * @author Bert Maurau
 */
class LogRequest extends BaseModel
{

    // |------------------------------------------------------------------------
    // |  Model Configuration
    // |------------------------------------------------------------------------
    // Reference to the Database table
    const DB_TABLE = "log_requests";
    // Define what is the primary key
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    const FILTERS = [];
    // Does the table have timestamps? (created_at, updated_at, deleted_at)
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [];
    // list of updatable fields
    const UPDATABLE = [];

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------
    // integer
    public $user_id;
    // string
    public $verb;
    // string
    public $uri;
    // string
    public $payload;
    // string
    public $headers;
    // string
    public $remote_address;

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    /**
     * Register the incoming request
     * @return void
     */
    public static function registerRequest()
    {

        // get the info about the request
        $verb = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $payload = file_get_contents('php://input');
        $rawHeaders = getallheaders();
        $headersJson = json_encode($rawHeaders);
        $remoteAddress = Core\Auth::getRemoteAddress();

        // cleanup excessive payloads
        if (strpos($payload, 'password') !== false) {
            $payload = '--SCREENED--';
        }
        if (strpos($payload, 'base64') !== false) {
            $payload = '--CLEANED--';
        }

        $userId = null;

        // check for headers and get user id
        if ($rawHeaders && isset($rawHeaders['Authorization']) && strpos($rawHeaders['Authorization'], 'Bearer') !== false) {
            // get the token
            try {
                $headers = null;
                if (isset($_SERVER['Authorization'])) {
                    $headers = trim($_SERVER["Authorization"]);
                } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // Nginx or fast CGI
                    $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
                } elseif (function_exists('apache_request_headers')) {
                    $requestHeaders = apache_request_headers();

                    // Server-side fix for bug in old Android versions (a nice side-effect of
                    // this fix means we don't care about capitalization for Authorization)
                    $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                    if (isset($requestHeaders['Authorization'])) {
                        $headers = trim($requestHeaders['Authorization']);
                    }
                }

                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    $token = $matches[1];

                    // why does it even get here when there is no token in header??????
                    if ($token && $token != "") {
                        $tokenProperties = (object) json_decode(\JWT::decode($token, Core\Config::getInstance() -> Salts() -> token));
                        $userId = isset($tokenProperties -> userId) ? (int) $tokenProperties -> userId : null;
                    }
                }
            } catch (\Exception $ex) {

            }
        }

        $log = (new LogRequest)
                -> setUser_id($userId)
                -> setVerb($verb)
                -> setUri($uri)
                -> setPayload($payload)
                -> setHeaders($headersJson)
                -> setRemote_address($remoteAddress)
                -> insert();
        return;
    }

    // |------------------------------------------------------------------------
    // |  Getters
    // |------------------------------------------------------------------------

    public function getUser_id()
    {
        return $this -> user_id;
    }

    /**
     * Get verb
     * @return string
     */
    public function getVerb()
    {
        return $this -> verb;
    }

    /**
     * Get uri
     * @return string
     */
    public function getUri()
    {
        return $this -> uri;
    }

    /**
     * Get payload
     * @return string
     */
    public function getPayload()
    {
        return $this -> payload;
    }

    /**
     * Get headers
     * @return string
     */
    public function getHeaders()
    {
        return $this -> headers;
    }

    /**
     * Get remote_address
     * @return string
     */
    public function getRemote_address()
    {
        return $this -> remote_address;
    }

    // |------------------------------------------------------------------------
    // |  Setters
    // |------------------------------------------------------------------------


    public function setUser_id($user_id)
    {
        $this -> user_id = (int) $user_id;
        return $this;
    }

    /**
     * Set verb
     * @param string $verb
     * @return $this
     */
    public function setVerb($verb)
    {
        $this -> verb = (string) $verb;
        return $this;
    }

    /**
     * Set uri
     * @param string $uri
     * @return $this
     */
    public function setUri($uri)
    {
        $this -> uri = (string) $uri;
        return $this;
    }

    /**
     * Set payload
     * @param string $payload
     * @return $this
     */
    public function setPayload($payload)
    {
        $this -> payload = (string) $payload;
        return $this;
    }

    /**
     * Set headers
     * @param string $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this -> headers = (string) $headers;
        return $this;
    }

    /**
     * Set remote_address
     * @param string $remote_address
     * @return $this
     */
    public function setRemote_address($remote_address)
    {
        $this -> remote_address = (string) $remote_address;
        return $this;
    }

}
