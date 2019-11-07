<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UrlRequest
 *
 * @author bertmaurau
 */
class UrlRequest extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "url_requests";
    // Define what the primary key is
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
     * Url ID
     * @var integer
     */
    private $url_id;

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Get URL ID
     *
     * @return int URL ID
     */
    public function getUrlId(): int
    {
        return $this -> url_id;
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
     * Set URL ID
     *
     * @param int $urlId url_id
     *
     * @return $this
     */
    public function setUrlId(int $urlId): UrlRequest
    {
        $this -> url_id = $urlId;

        return $this;
    }

    /**
     * Set Remote Address
     *
     * @param string $remoteAddress remote_address
     *
     * @return $this
     */
    public function setRemoteAddress(string $remoteAddress): UrlRequest
    {
        $this -> remote_address = $remoteAddress;

        return $this;
    }

}
