<?php

namespace BertMaurau\URLShortener\Models;

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
    public function getUrlsId(): int
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
    public function setUrlAliasId(int $urlId): UserUrl
    {
        $this -> url_id = $urlId;

        return $this;
    }

}
