<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UserUrlAlias
 *
 * @author bertmaurau
 */
class UserUrlAlias extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "user_url_aliasses";
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
    private $user_id;

    /**
     * URL Alias ID
     * @var int
     */
    public $url_alias_id;

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
     * Get URL Alias ID
     *
     * @return int URL Alias ID
     */
    public function getUrlAliasId(): int
    {
        return $this -> url_alias_id;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return $this
     */
    public function setUserId(int $userId): UserUrlAlias
    {
        $this -> userId = $userId;

        return $this;
    }

    /**
     * Set URL Alias ID
     *
     * @param int $urlAliasId url_alias_id
     *
     * @return $this
     */
    public function setUrlAliasId(int $urlAliasId): UserUrlAlias
    {
        $this -> urlAliasId = $urlAliasId;

        return $this;
    }

}
