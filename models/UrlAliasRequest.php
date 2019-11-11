<?php

namespace BertMaurau\URLShortener\Models;

require_once 'UrlRequest.php';

/**
 * Description of UrlAliasRequest
 *
 * @author bertmaurau
 */
class UrlAliasRequest extends UrlRequest
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
    protected $url_alias_id;

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

}
