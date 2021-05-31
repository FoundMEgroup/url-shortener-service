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
        'url'        => '',
        'short_code' => '',
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
     * Leadcamp User ID
     * @var int
     */
    public $leadcamp_user_id;

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
     * Redirect to the URL and exit script
     *
     * @return void
     */
    public function redirectToUrl(): void
    {

        // get ptotal amount of clicks
        $clicks = UrlRequest::getClicksForUrlId($this -> getId());

        // append to url
        $url = $this -> getUrl() . (parse_url($this -> getUrl(), PHP_URL_QUERY) ? '&' : '?') . 'first=' . ($clicks == 0 ? 'true' : 'false');

        // check if url has protocol
        $url = $this -> addScheme($url);

        // set the url for future use
        $this -> setUrl($url);

//        if ($this -> getBrowserDetect()) {
//            // render javascript page to get/fetch browser info that sends
//            // client info to this api for this url and then redirects
//
//            Core\BrowserDetect::render($this -> getUrl(), $urlRequest -> getGuid());
//
//            // do tracker magic
//            Core\UrlTracker::track($urlRequest);
//
//            //
//        } else {
        if (!headers_sent()) {

            // start the forced redirect
            header('Connection: close');
            ob_start();
            header('Content-Length: 0');
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: {$this -> getUrl()}");
            ob_end_flush();
            flush();
            // end the forced redirect and continue with this script process

            ignore_user_abort(true);

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
//        }
        exit();
    }

    private function addScheme(string $url, string $scheme = 'https://')
    {
        return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }

    /**
     * Get Leadcamp User ID
     *
     * @return int
     */
    function getLeadcampUserId()
    {
        return $this -> leadcamp_user_id;
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
     * Set Leadcamp User ID
     *
     * @param int $leadcampUserId
     *
     * @return void
     */
    function setLeadcampUserId(int $leadcampUserId = null): Url
    {
        $this -> leadcamp_user_id = $leadcampUserId;
        return $this;
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

}
