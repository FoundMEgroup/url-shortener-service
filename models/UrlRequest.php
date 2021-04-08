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
    const BOT_USER_AGENTS = [
        "360Spider",
        "AdsBot",
        "adidxbot",
        "Applebot",
        "AppleNewsBot",
        "Baiduspider",
        "bingbot",
        "BingPreview",
        "BublupBot",
        "CCBot",
        "Cliqzbot",
        "coccoc",
        "coccocbot",
        "Daumoa",
        "Dazoobot",
        "DeuSu",
        "DuckDuckBot",
        "DuckDuckGo-Favicons-Bot",
        "EuripBot",
        "Exploratodo",
        "Facebot",
        "facebook",
        "Feedly",
        "Findxbot",
        "Googlebot",
        "HaoSouSpider",
        "ichiro",
        "istellabot",
        "JikeSpider",
        "Lycos",
        "Mail.Ru",
        "Mediapartners-Google",
        "MojeekBot",
        "msnbot",
        "OrangeBot",
        "Pinterest",
        "Plukkie",
        "Qwantify",
        "Rambler",
        "SeznamBot",
        "Sosospider",
        "Slackbot",
        "Slurp",
        "Sogou",
        "SputnikBot",
        "Teoma",
        "Twitterbot",
        "wotbox",
        "yacybot",
        "Yandex",
        "Yeti",
        "YioopBot",
        "yoozBot",
        "YoudaoBot",
    ];

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
     * Get total amount of clicks for given URL ID
     *
     * @param int $urlId
     *
     * @return int
     */
    public static function getClicksForUrlId(int $urlId)
    {
        $query = "
            SELECT COUNT(*) as clicks FROM url_requests WHERE url_id = " . Core\Database::escape($urlId) . ";
            ";
        $result = Core\Database::query($query);
        return (int) ($result -> fetch_assoc())['clicks'] ?? 0;
    }

    /**
     * Check if given User Agent would be from a bot/rich preview
     *
     * @param string $ua
     * 
     * @return boolean
     */
    public static function isRequestFromBot(string $ua = null)
    {
        if (!$ua) {
            return false;
        }

        foreach (self::BOT_USER_AGENTS as $ua) {
            if (stripos($ua, $ua) !== false)
                return true;
        }
        return false;
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
