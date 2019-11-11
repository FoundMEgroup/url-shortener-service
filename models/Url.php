<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of Url
 *
 * @author bertmaurau
 */
class Url extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "urls";
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
        'url' => [true, 'string', 1, 256],
    ];
    // list of updatable fields
    const UPDATABLE = ['url' => ''];

    /**
     * Short Code
     * @var string
     */
    public $short_code;

    /**
     * URL
     * @var string
     */
    public $url;

    public function redirectToUrl()
    {
        if (!headers_sent()) {
            header("Location: {$this -> getUrl()}");
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $this -> getUrl() . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . $this -> getUrl() . '" />';
            echo '</noscript>';
        }
        exit();
    }

    /**
     * Get Short Code
     *
     * @return string Short Code
     */
    public function getShortCode(): string
    {
        return $this -> short_code;
    }

    /**
     * Get URL
     *
     * @return string URL
     */
    public function getUrl(): string
    {
        return $this -> url;
    }

    /**
     * Set Short Code
     *
     * @param string $shortCode short_code
     *
     * @return $this
     */
    public function setShortCode(string $shortCode = null): Url
    {
        $this -> short_code = $shortCode;

        return $this;
    }

    /**
     * Set URL
     *
     * @param string $url url
     *
     * @return $this
     */
    public function setUrl(string $url): Url
    {
        $this -> url = $url;

        return $this;
    }

}
