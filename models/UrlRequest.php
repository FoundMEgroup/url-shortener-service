<?php

namespace BertMaurau\URLShortener\Models;

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
     * Country ISO
     * @var string
     */
    public $country_iso;

    /**
     * Country Name
     * @var string
     */
    public $country_name;

    /**
     * Division ISO
     * @var string
     */
    public $division_iso;

    /**
     * Division Name
     * @var string
     */
    public $division_name;

    /**
     * City
     * @var string
     */
    public $city;

    /**
     * Postal Code
     * @var string
     */
    public $postal_code;

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
    public function getRemoteAddress(): string
    {
        return $this -> remote_address;
    }

    /**
     * Get Country ISO
     *
     * @return string
     */
    public function getCountryIso(): string
    {
        return $this -> country_iso;
    }

    /**
     * Get Country Name
     *
     * @return string
     */
    public function getCountryName(): string
    {
        return $this -> country_name;
    }

    /**
     * Get Division ISO
     *
     * @return string
     */
    public function getDivisionIso(): string
    {
        return $this -> division_iso;
    }

    /**
     * Get Division Name
     *
     * @return string
     */
    public function getDivisionName(): string
    {
        return $this -> division_name;
    }

    /**
     * Get City
     *
     * @return string
     */
    public function getCity(): string
    {
        return $this -> city;
    }

    /**
     * Get Postal Code
     *
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this -> postal_code;
    }

    /**
     * Get Latitude
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this -> latitude;
    }

    /**
     * Get Longitude
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this -> longitude;
    }

    /**
     * Set URL ID
     *
     * @param int $urlId url_id
     *
     * @return $this
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
     * @return UrlRequest
     */
    public function setRemoteAddress(string $remoteAddress): UrlRequest
    {
        $this -> remote_address = $remoteAddress;

        return $this;
    }

    /**
     * Set Country ISO
     *
     * @param string $countryIso country_iso
     *
     * @return UrlRequest
     */
    public function setCountryIso(string $countryIso): UrlRequest
    {
        $this -> country_iso = $countryIso;

        return $this;
    }

    /**
     * Set Country Name
     *
     * @param string $countryName country_name
     *
     * @return UrlRequest
     */
    public function setCountryName(string $countryName): UrlRequest
    {
        $this -> country_name = $countryName;

        return $this;
    }

    /**
     * Set Division ISO
     *
     * @param string $divisionIso division_iso
     *
     * @return UrlRequest
     */
    public function setDivisionIso(string $divisionIso): UrlRequest
    {
        $this -> division_iso = $divisionIso;

        return $this;
    }

    /**
     * Set Division Name
     *
     * @param string $divisionName division_name
     *
     * @return UrlRequest
     */
    public function setDivisionName(string $divisionName): UrlRequest
    {
        $this -> division_name = $divisionName;

        return $this;
    }

    /**
     * Set City
     *
     * @param string $city city
     *
     * @return UrlRequest
     */
    public function setCity(string $city): UrlRequest
    {
        $this -> city = $city;

        return $this;
    }

    /**
     * Set Postal Code
     *
     * @param string $postalCode postal_code
     *
     * @return UrlRequest
     */
    public function setPostalCode(string $postalCode): UrlRequest
    {
        $this -> postal_code = $postalCode;

        return $this;
    }

    /**
     * Set Latitude
     *
     * @param float $latitude latitude
     *
     * @return UrlRequest
     */
    public function setLatitude(float $latitude): UrlRequest
    {
        $this -> latitude = $latitude;

        return $this;
    }

    /**
     * Set Longitude
     *
     * @param float $longitude longitude
     *
     * @return UrlRequest
     */
    public function setLongitude(float $longitude): UrlRequest
    {
        $this -> longitude = $longitude;

        return $this;
    }

}
