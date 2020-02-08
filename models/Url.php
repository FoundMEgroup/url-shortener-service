<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core AS Core;

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
    const FILTERS = [
        'url'            => '',
        'short_code'     => '',
        'browser_detect' => '',
    ];
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

    /**
     * Browser Detect
     * @var boolean
     */
    public $browser_detect;

    /**
     * Redirect to the URL and exit script
     *
     * @param UrlRequest $urlRequest The URL Request to use for the tracker
     *
     * @return void
     */
    public function redirectToUrl(UrlRequest $urlRequest): void
    {
        if ($this -> getBrowserDetect()) {
            // render javascript page to get/fetch browser info that sends
            // client info to this api for this url and then redirects

            Core\BrowserDetect::render($this -> getUrl(), $urlRequest -> getGuid());

            // do tracker magic
            Core\UrlTracker::track($urlRequest);

            //
        } else {
            if (!headers_sent()) {

                // start the forced redirect
                header('Connection: close');
                ob_start();
                header('Content-Length: 0');
                header("Location: {$this -> getUrl()}");
                ob_end_flush();
                flush();
                // end the forced redirect and continue with this script process

                ignore_user_abort(true);

                // do tracker magic
                Core\UrlTracker::track($urlRequest);

                //
            } else {

                // fallback script
                echo '<script type="text/javascript">';
                echo '  window.location.href="' . $this -> getUrl() . '";';
                echo '</script>';
                echo '<noscript>';
                echo '  <meta http-equiv="refresh" content="0;url=' . $this -> getUrl() . '" />';
                echo '</noscript>';
            }
        }
        exit();
    }

    /**
     * Get model by specific field for User ID
     *
     * @param array $fieldsWithValues List of fields to filter on
     * @param int $take Pagination take
     * @param int $skip Pagination skip
     *
     * @return $this
     */
    public function findByUserId(int $userId, array $fieldsWithValues = array(), $take = 120, $skip = 0)
    {
        // check if the requested field exists for this model
        $conditions = [];
        foreach ($fieldsWithValues as $field => $value) {
            if (!array_key_exists($field, get_object_vars($this))) {
                throw new \Exception("`" . $field . "` is not a recognized property.");
            } else {
                if ($field !== 'attributes' && !is_null($value) && !empty($value) && is_callable(array($this, 'get' . ucfirst($field)))) {
                    $conditions[] = "urls.`" . $field . "` = '" . Core\Database::escape($value) . "'";
                }
            }
        }

        $take = $take ?? 120;
        $skip = $skip ?? 0;

        $query = " SELECT "
                . " urls.*, "
                . " user_urls.id AS user_url_id, "
                . " user_urls.created_at AS user_url_created_at, "
                . " user_urls.updated_at AS user_url_updated_at "
                . "FROM urls "
                . "LEFT JOIN user_urls ON user_urls.url_id = urls.id "
                . "WHERE user_urls.user_id =  " . Core\Database::escape($userId) . " " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND " . static::DB_TABLE . ".deleted_at IS NULL " : "")
                . "LIMIT $take OFFSET $skip;";
        $result = Core\Database::query($query);
        if ($take && $take === 1) {
            if ($result -> num_rows < 1) {
                return false;
            } else {
                return $this -> map($result -> fetch_assoc());
            }
        } else {
            $response = [];
            while ($row = $result -> fetch_assoc()) {
                $url = (new $this) -> map($row);

                $url -> deleteAttribute('user_url_id');
                $url -> deleteAttribute('user_url_created_at');
                $url -> deleteAttribute('user_url_updated_at');

                $userUrl = (new UserUrl) -> map([
                    'id'         => $row['user_url_id'],
                    'created_at' => $row['user_url_created_at'],
                    'updated_at' => $row['user_url_updated_at'],
                ]);

                $url -> addAttribute('user_url', $userUrl);

                $response[] = $url;
            }
            return $response;
        }
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
     * Get Browser Detect
     *
     * @return bool Browser Detect
     */
    public function getBrowserDetect(): bool
    {
        return $this -> browser_detect;
    }

    /**
     * Set Short Code
     *
     * @param string $shortCode short_code
     *
     * @return \BertMaurau\URLShortener\Models\Url
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
     * @return \BertMaurau\URLShortener\Models\Url
     */
    public function setUrl(string $url): Url
    {
        $this -> url = $url;

        return $this;
    }

    /**
     * Set Browser Detect
     *
     * @param bool $browserDetect browser_detect
     *
     * @return \BertMaurau\URLShortener\Models\Url
     */
    public function setBrowserDetect(bool $browserDetect): Url
    {
        $this -> browser_detect = $browserDetect;

        return $this;
    }

}
