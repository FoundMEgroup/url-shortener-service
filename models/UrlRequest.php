<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core AS Core;

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
    protected $url_id;

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Init a new UrlRequest used for the tracker
     *
     * @param int $urlId The URL ID
     * @param int $urlAliasId The URL Alias ID
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public static function init(int $urlId, int $urlAliasId = null): UrlRequest
    {
        return (new self)
                        -> setUrlId($urlId)
                        -> setRemoteAddress(Core\Auth::getRemoteAddress())
                        -> insert();
    }

    /**
     * Get list of last requests for given Url ID
     *
     * @param int $urlId
     * @param int $take
     *
     * @return array
     */
    public function getLastRequestsForUrlId(int $urlId, int $take = 50): array
    {
        $response = [];
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE url_id = " . Core\Database::escape($value) . " "
                . ((static::SOFT_DELETES) ? " AND " . static::DB_TABLE . ".deleted_at IS NULL " : "")
                . "ORRDER BY id DESC "
                . "LIMIT $take;";

        $result = Core\Database::query($query);
        while ($row = $result -> fetch_assoc()) {
            $resource = (new $this) -> map($row);
            $response[] = $resource;
        }
        return $response;
    }

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
     * @return string
     */
    public function getRemoteAddress(): ?string
    {
        return $this -> remote_address;
    }

    /**
     * Set URL ID
     *
     * @param int $urlId url_id
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
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
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setRemoteAddress(string $remoteAddress = null): UrlRequest
    {
        $this -> remote_address = $remoteAddress;

        return $this;
    }

}
