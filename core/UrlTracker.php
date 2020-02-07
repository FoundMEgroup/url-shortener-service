<?php

namespace BertMaurau\URLShortener\Core;

use BertMaurau\URLShortener\Models AS Models;
use GeoIp2\Database\Reader;

/**
 * Description of UrlTracker
 *
 * @author bertmaurau
 */
class UrlTracker
{

    /**
     * Track the request
     *
     * @param Models\UrlRequest $urlRequest
     *
     * @return void
     */
    public static function track(Models\UrlRequest $urlRequest): void
    {

        if ($urlRequest -> getRemoteAddress()) {

            // get location info for remote address
            try {

                $locationData = self::getLocationData($urlRequest -> getRemoteAddress());
                if ($locationData) {
                    $urlRequest
                            -> setGeolocation($locationData['data'] ?? null)
                            -> setCountryIso($locationData['countryIso'] ?? null)
                            -> setCity($locationData['city'] ?? null)
                            -> setLatitude($locationData['latitude'] ?? null)
                            -> setLongitude($locationData['longitude'] ?? null);
                }
            } catch (\Exception $ex) {

            }
        }

        $urlRequest -> update();

        return;
    }

    /**
     * Get location based on remote address
     *
     * @param string $remoteAddress
     *
     * @return array
     */
    public static function getLocationData(string $remoteAddress): array
    {

        $result = [];

        try {
            $geoResponse = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $remoteAddress));
            if ($geoResponse) {
                $result['countryIso'] = $geoResponse['geoplugin_countryCode'] ?? null;
                $result['city'] = $geoResponse['geoplugin_city'] ?? null;
                $result['longitude'] = $geoResponse['geoplugin_longitude'] ?? null;
                $result['latitude'] = $geoResponse['geoplugin_latitude'] ?? null;
                $result['data'] = $geoResponse;
            }
        } catch (\Exception $ex) {
            if (file_exists($remoteAddress)) {
                $reader = new Reader(Config::getInstance() -> Paths() -> statics . 'geo-data/GeoLite2-City.mmdb');

                $ctiy = $reader -> city($remoteAddress);

                $result['countryIso'] = $ctiy -> country -> isoCode ?? null;
                $result['city'] = $ctiy -> city -> name ?? null;
                $result['longitude'] = $ctiy -> location -> latitude ?? null;
                $result['latitude'] = $ctiy -> location -> longitude ?? null;
                $result['data'] = $ctiy;
            }
        }


        return $result;
    }

}
