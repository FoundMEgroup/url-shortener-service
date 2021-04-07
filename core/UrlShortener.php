<?php

namespace BertMaurau\URLShortener\Core;

use BertMaurau\URLShortener\Models AS Models;

/**
 * Description of Shortener
 *
 * @author bertmaurau
 */
class UrlShortener
{

    /**
     * Create a new URL
     *
     * @param string $extUrl The URL to shorten
     * @param int $userId The User ID (if created by an authenticated user)
     *
     * @return Url
     */
    public static function create(string $extUrl, int $userId = null)
    {

        // create a new URL entry
        $url = (new Models\Url)
                -> setUrl($extUrl)
                -> insert();

        // create a new Hash ID from the ID
        $shortCode = Generator::HashId($url -> getId());

        // update the record
        $url -> setShortCode($shortCode) -> setLeadcampUserId($userId) -> update();

        return $url;
    }

}
