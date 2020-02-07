<?php

namespace BertMaurau\URLShortener\Core;

/**
 * Description of Config
 *
 * This handles everything concerning the configuration.
 *
 * @author Bert Maurau
 */
class Config
{

    /**
     * The Instance
     * @var Config
     */
    private static $instance;

    /**
     * Database
     * @var stdClass
     */
    private $database;

    /**
     * API
     * @var stdClass
     */
    private $api;

    /**
     * Token
     * @var stdClass
     */
    private $token;

    /**
     * SMTP
     * @var stdClass
     */
    private $smtp;

    /**
     * Salts
     * @var stdClass
     */
    private $salts;

    /**
     * Paths
     * @var stdClass
     */
    private $paths;

    /**
     * Hash IDs
     * @var stdClass
     */
    private $hashId;

    public function __construct()
    {
        // load the .env
        $dotenv = \Dotenv\Dotenv::create(__DIR__ . '/../env/');
        $dotenv -> load();

        // load the api-config
        $this -> api = (object) [
                    'root'    => getenv('API_ROOT'),
                    'baseUrl' => getenv('BASE_URL'),
                    'env'     => getenv('ENV'),
        ];

        // load the databse-config
        $this -> database = (object) [
                    'host'    => getenv('DATABASE_HOST'),
                    'user'    => getenv('DATABASE_USER'),
                    'pass'    => getenv('DATABASE_PASS'),
                    'name'    => getenv('DATABASE_NAME'),
                    'charset' => getenv('DATABASE_CHARSET'),
        ];

        // load the databse-config
        $this -> smtp = (object) [
                    'host'   => getenv('SMTP_HOST'),
                    'user'   => getenv('SMTP_USER'),
                    'pass'   => getenv('SMTP_PASS'),
                    'secure' => getenv('SMTP_SECURE'),
                    'port'   => getenv('SMTP_PORT'),
        ];

        // load the token-config
        $this -> token = (object) [
                    'urlGetParameterAllowed' => getenv('URL_GET_TOKEN_ALLOWED'),
                    'urlGetParameter'        => getenv('URL_GET_TOKEN_PARAMETER'),
        ];

        // load the salts-config
        $this -> salts = (object) [
                    'token'    => getenv('JWT_SECRET'),
                    'password' => getenv('PASSWORD_SALT'),
        ];

        // load the paths-config
        $this -> paths = (object) [
                    'statics'       => __DIR__ . '/..' . getenv('PATH_STATICS'),
                    'statics_url'   => getenv('BASE_URL') . getenv('PATH_STATICS'),
                    'images'        => __DIR__ . '/..' . getenv('PATH_STATICS_IMAGES'),
                    'images_url'    => getenv('BASE_URL') . getenv('PATH_STATICS_IMAGES'),
                    'templates'     => __DIR__ . '/..' . getenv('PATH_STATICS_TEMPLATES'),
                    'templates_url' => getenv('BASE_URL') . getenv('PATH_STATICS_TEMPLATES'),
                    'base_url'      => getenv('BASE_URL'),
        ];

        // load the salts-config
        $this -> hashId = (object) [
                    'seed'     => getenv('HASH_ID_SEED'),
                    'length'   => getenv('HASH_ID_LENGTH'),
                    'alphabet' => getenv('HASH_ID_ALPHABET'),
        ];
    }

    /**
     * Create a new instance
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
     * Return the database-config
     * @return object
     */
    public function Database()
    {
        return $this -> database;
    }

    /**
     * Return the api-config
     * @return object
     */
    public function API()
    {
        return $this -> api;
    }

    /**
     * Return the token-config
     * @return object
     */
    public function Token()
    {
        return $this -> token;
    }

    /**
     * Return the smtp-config
     * @return object
     */
    public function SMTP()
    {
        return $this -> smtp;
    }

    /**
     * Return the salts-config
     * @return object
     */
    public function Salts()
    {
        return $this -> salts;
    }

    /**
     * Return the paths-config
     * @return object
     */
    public function Paths()
    {
        return $this -> paths;
    }

    /**
     * Return the hashId-config
     * @return object
     */
    public function HashId()
    {
        return $this -> hashId;
    }

    /**
     * Get the protocol
     * @return string
     */
    private function getServerProtocol()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    }

    /**
     * Get the current domain
     * @return string
     */
    private function getServerDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

}
