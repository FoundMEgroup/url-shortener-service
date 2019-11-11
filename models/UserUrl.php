<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core AS Core;

/**
 * Description of UserUrl
 *
 * @author bertmaurau
 */
class UserUrl extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "user_urls";
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
     * User ID
     * @var int
     */
    protected $user_id;

    /**
     * URL ID
     * @var int
     */
    public $url_id;

    /**
     * Get list of user Urls with basic counts
     *
     * @param int $userId
     *
     * @return array
     */
    public function getOverviewByUserId(int $userId): array
    {
        $query = "
            SELECT
                user_urls.*,
                urls.url AS url,
                urls.short_code AS short_code,
                SUM(IF(url_requests.id, 1, 0)) AS url_requests,
                SUM(IF(url_requests.url_alias_id, 1, 0)) AS url_requests_by_alias
            FROM user_urls
                LEFT JOIN urls ON urls.id = user_urls.url_id
                LEFT JOIN url_requests ON url_requests.url_id = urls.id
            WHERE user_urls.user_id = " . Core\Database::escape($userId) . "
            GROUP BY urls.id
            ORDER BY user_urls.created_at DESC;
            ";
        $result = Core\Database::query($query);

        $output = [];
        while ($row = $result -> fetch_assoc()) {
            $res = (new $this) -> map($row);

            $output[] = $res;
        }

        return $output;
    }

    /**
     * Delete everything related to this User URL
     */
    public function deleteFull()
    {
        // delete url
        $url = (new Url) -> findByAndDelete(['id' => $this -> getUrlId()]);
        // delete url requests
        // $url = (new UrlRequest) -> findByAndDelete(['url_id' => $this -> getUrlId()]);
        // delete url aliasses
        $url = (new UrlAlias) -> findByAndDelete(['url_id' => $this -> getUrlId()]);
        // delete url aliasses requests
        // $url = (new UrlAliasRequest) -> findByAndDelete(['url_id' => $this -> getUrlId()]);
        // delete user url
        $url = (new UserUrl) -> findByAndDelete(['url_id' => $this -> getUrlId()]);
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
     * Get URL ID
     *
     * @return int URL ID
     */
    public function getUrlId(): int
    {
        return $this -> url_id;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return $this
     */
    public function setUserId(int $userId): UserUrl
    {
        $this -> user_id = $userId;

        return $this;
    }

    /**
     * Set URL ID
     *
     * @param int $urlId url_id
     *
     * @return $this
     */
    public function setUrlId(int $urlId): UserUrl
    {
        $this -> url_id = $urlId;

        return $this;
    }

}
