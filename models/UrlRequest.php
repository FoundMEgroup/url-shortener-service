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
     * Url Alias ID
     * @var integer
     */
    public $url_alias_id;

    /**
     * GUID
     * @var string
     */
    public $guid;

    /**
     * Remote Address
     * @var string
     */
    public $remote_address;

    /**
     * Browser Detect
     * @var \stdClass
     */
    public $browser_detect;

    /**
     * Language
     * @var string
     */
    public $language;

    /**
     * Platofmr
     * @var string
     */
    public $platform;

    /**
     * Browserr
     * @var string
     */
    public $browser;

    /**
     * Geolocation
     * @var \stdClass
     */
    public $geolocation;

    /**
     * Country ISO
     * @var string
     */
    public $country_iso;

    /**
     * Region
     * @var string
     */
    public $region;

    /**
     * City
     * @var string
     */
    public $city;

    /**
     * Latitude
     * @var float
     */
    public $latitude;

    /**
     * Longitude
     * @var float
     */
    public $longitude;

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
                        -> setUrlAliasId($urlAliasId)
                        -> setGuid(Core\Generator::GUIDv4())
                        -> setRemoteAddress(Core\Auth::getRemoteAddress())
                        -> insert();
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
     * Get URL Alias ID
     *
     * @return int URL Alias ID
     */
    public function getUrlAliasId(): ?int
    {
        return $this -> url_alias_id;
    }

    /**
     * Get GUID
     *
     * @return string GUID
     */
    public function getGuid(): string
    {
        return $this -> guid;
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
     * Get Browser Detect
     *
     * @return \stdClass
     */
    public function getBrowserDetect(): ?\stdClass
    {
        return $this -> browser_detect;
    }

    /**
     * Get Language
     *
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this -> language;
    }

    /**
     * Get Platform
     *
     * @return string
     */
    public function getPlatform(): ?string
    {
        return $this -> platform;
    }

    /**
     * Get Browser
     *
     * @return string
     */
    public function getBrowser(): ?string
    {
        return $this -> browser;
    }

    /**
     * Get Geolocation
     * @return s\stdClass
     */
    public function getGeolocation(): ?\stdClass
    {
        return $this -> geolocation;
    }

    /**
     * Get Country ISO
     *
     * @return string
     */
    public function getCountryIso(): ?string
    {
        return $this -> country_iso;
    }

    /**
     * Get Region
     *
     * @return string
     */
    public function getRegion(): string
    {
        return $this -> region;
    }

    /**
     * Get City
     *
     * @return string
     */
    public function getCity(): ?string
    {
        return $this -> city;
    }

    /**
     * Get Latitude
     *
     * @return float
     */
    public function getLatitude(): ?float
    {
        return $this -> latitude;
    }

    /**
     * Get Longitude
     *
     * @return float
     */
    public function getLongitude(): ?float
    {
        return $this -> longitude;
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
     * Set URL Alias ID
     *
     * @param int $urlAliasId url_alias_id
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setUrlAliasId(int $urlAliasId = null): UrlRequest
    {
        $this -> url_alias_id = $urlAliasId;

        return $this;
    }

    /**
     * Set GUID
     *
     * @param string $guid guid
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setGuid(string $guid): UrlRequest
    {
        $this -> guid = $guid;

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

    /**
     * Set Browser Detect
     *
     * @param mixed $browserDetect browser_detect
     *
     * @return \stdClass|null
     */
    public function setBrowserDetect($browserDetect = null): UrlRequest
    {
        if (is_string($browserDetect)) {
            $browserDetect = json_decode($browserDetect);
            if ($browserDetect === null && json_last_error() !== JSON_ERROR_NONE) {
                $browserDetect = null;
            }
        }
        $this -> browser_detect = $browserDetect;
        return $this;
    }

    /**
     * Set Language
     *
     * @param string $language language
     * 
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setLanguage(string $language = null): UrlRequest
    {
        $this -> language = $language;

        return $this;
    }

    /**
     * Set Platform
     *
     * @param string $platform platform
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setPlatform(string $platform = null): UrlRequest
    {
        $this -> platform = $platform;

        return $this;
    }

    /**
     * Set Browser
     *
     * @param string $browser browser
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setBrowser(string $browser = null): UrlRequest
    {
        $this -> browser = $browser;

        return $this;
    }

    /**
     * Set Geolocation
     *
     * @param mixed $geolocation geolocation
     *
     * @return \stdClass|null
     */
    public function setGeolocation($geolocation = null): UrlRequest
    {
        if (is_string($geolocation)) {
            $geolocation = json_decode($geolocation);
            if ($geolocation === null && json_last_error() !== JSON_ERROR_NONE) {
                $geolocation = null;
            }
        }
        $this -> geolocation = $geolocation;
        return $this;
    }

    /**
     * Set Country ISO
     *
     * @param string $countryIso country_iso
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setCountryIso(string $countryIso = null): UrlRequest
    {
        $this -> country_iso = $countryIso;

        return $this;
    }

    /**
     * Set Region
     *
     * @param string $region region
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setRegion(string $region = null): UrlRequest
    {
        $this -> region = $region;
        return $this;
    }

    /**
     * Set City
     *
     * @param string $city city
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setCity(string $city = null): UrlRequest
    {
        $this -> city = $city;

        return $this;
    }

    /**
     * Set Latitude
     *
     * @param float $latitude latitude
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setLatitude(float $latitude = null): UrlRequest
    {
        $this -> latitude = $latitude;

        return $this;
    }

    /**
     * Set Longitude
     *
     * @param float $longitude longitude
     *
     * @return \BertMaurau\URLShortener\Models\UrlRequest
     */
    public function setLongitude(float $longitude = null): UrlRequest
    {
        $this -> longitude = $longitude;

        return $this;
    }

}
