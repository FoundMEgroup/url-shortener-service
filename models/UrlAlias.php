<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UrlAlias
 *
 * @author bertmaurau
 */
class UrlAlias extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "url_aliasses";
    // Define what the primary key is
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    const FILTERS = [];
    // Does the table have timestamps? (created_at, updated_at, deleted_at)
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [
        'alias' => [true, 'string', 1, 128],
    ];
    // list of updatable fields
    const UPDATABLE = ['alias' => ''];

    /**
     * Url ID
     * @var int
     */
    protected $url_id;

    /**
     * Alias
     * @var string
     */
    public $alias;

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
     * Get Alias
     *
     * @return string Alias
     */
    public function getAlias(): string
    {
        return $this -> alias;
    }

    /**
     * Set URL ID
     *
     * @param int $urlId url_id
     *
     * @return $this
     */
    public function setUrlId(int $urlId): UrlAlias
    {
        $this -> url_id = $urlId;

        return $this;
    }

    /**
     * Set Alias
     *
     * @param string $alias Alias
     *
     * @return $this
     */
    public function setAlias(string $alias): UrlAlias
    {
        $this -> alias = $alias;

        return $this;
    }

}
