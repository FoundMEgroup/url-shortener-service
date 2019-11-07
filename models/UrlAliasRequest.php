<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UrlAliasRequest
 *
 * @author bertmaurau
 */
class UrlAliasRequest extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "url_alias_requests";
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
     * Url Alias ID
     * @var int
     */
    private $url_alias_id;

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Get URL Alias ID
     *
     * @return int URL Alias ID
     */
    public function getUrlAliasId(): int
    {
        return $this -> url_alias_id;
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
     * Set URL Alias ID
     *
     * @param int $urlId url_alias_id
     *
     * @return $this
     */
    public function setUrlAliasId(int $urlAliasId): UrlAliasRequest
    {
        $this -> url_alias_id = $urlAliasId;

        return $this;
    }

    /**
     * Set Remote Address
     *
     * @param string $remoteAddress remote_address
     *
     * @return $this
     */
    public function setRemoteAddress(string $remoteAddress): UrlAliasRequest
    {
        $this -> remote_address = $remoteAddress;

        return $this;
    }

}
