<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core as Core;
use BertMaurau\URLShortener\Modules as Modules;

/**
 * Description of LogRequest
 *
 * @author Bert Maurau
 */
class LogRequest extends BaseModel
{

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

    /**
     * LogRequest
     * @var LogRequest
     */
    private static $instance;

    /**
     * User ID
     * @var integer
     */
    public $user_id;

    /**
     * Verb
     * @var string
     */
    public $verb;

    /**
     * URI
     * @var string
     */
    public $uri;

    /**
     * Payload
     * @var string
     */
    public $payload;

    /**
     * Headers
     * @var string
     */
    public $headers;

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Register the incoming request
     *
     * @return void
     */
    public static function register()
    {

        // get the info about the request
        $verb = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $rawPayload = json_encode(file_get_contents('php://input'));
        $rawHeaders = json_encode(getallheaders());
        $remoteAddress = Core\Auth::getRemoteAddress();

        // cleanup excessive payloads
        if (strpos($rawPayload, 'password') !== false) {
            $rawPayload = '--SCREENED--';
        }
        if (strpos($rawPayload, 'base64') !== false) {
            $rawPayload = '--CLEANED--';
        }

        $userId = null;

        try {
            $token = Core\Auth::getBearerToken();

            if ($token && $token != "") {
                $tokenData = (object) Modules\JWT::decode($token, Core\Config::getInstance() -> Salts() -> token);

                $userId = isset($tokenData -> userId) ? (int) $tokenData -> userId : null;
            }
        } catch (Exception $ex) {

        }

        $_logRequest = (new self)
                -> setUserId($userId)
                -> setVerb($verb)
                -> setUri($uri)
                -> setPayload($rawPayload)
                -> setHeaders($rawHeaders)
                -> setRemoteAddress($remoteAddress)
                -> insert();

        self::$instance = $_logRequest;

        return;
    }

    /**
     * Create a new instance
     *
     * @return instance
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get User ID
     *
     * @return int User ID
     */
    public function getUserId(): int
    {
        return $this -> user_id;
    }

    /**
     * Get Verb
     *
     * @return string Verb
     */
    public function getVerb(): string
    {
        return $this -> verb;
    }

    /**
     * Get URI
     *
     * @return string URI
     *
     */
    public function getUri(): string
    {
        return $this -> uri;
    }

    /**
     * Get p]Payload
     *
     * @return string Payload
     */
    public function getPayload(): string
    {
        return $this -> payload;
    }

    /**
     * Get Headers
     *
     * @return string Headers
     */
    public function getHeaders(): string
    {
        return $this -> headers;
    }

    /**
     * Get Remote Address
     *
     * @return string Remote Address
     */
    public function getRemoteAddress(): string
    {
        return $this -> remote_address;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return $this
     */
    public function setUserId(int $userId = null): LogRequest
    {
        $this -> user_id = $userId;

        return $this;
    }

    /**
     * Set Verb
     *
     * @param string $verb verb
     *
     * @return $this
     */
    public function setVerb(string $verb): LogRequest
    {
        $this -> verb = $verb;

        return $this;
    }

    /**
     * Set URI
     *
     * @param string $uri uri
     *
     * @return $this
     */
    public function setUri(string $uri): LogRequest
    {
        $this -> uri = $uri;

        return $this;
    }

    /**
     * Set Payload
     *
     * @param string $payload payload
     *
     * @return $this
     */
    public function setPayload(string $payload): LogRequest
    {
        $this -> payload = $payload;

        return $this;
    }

    /**
     * Set Headers
     *
     * @param string $headers headers
     *
     * @return $this
     */
    public function setHeaders(string $headers): LogRequest
    {
        $this -> headers = $headers;

        return $this;
    }

    /**
     * Set Remote Address
     *
     * @param string $remoteAddress remote_address
     *
     * @return $this
     */
    public function setRemoteAddress(string $remoteAddress): LogRequest
    {
        $this -> remote_address = $remoteAddress;

        return $this;
    }

}
