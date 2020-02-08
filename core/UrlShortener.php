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
    public static function create(string $extUrl, int $userId = null, bool $browserDetect = false)
    {

        // create a new URL entry
        $url = (new Models\Url)
                -> setUrl($extUrl)
                -> setBrowserDetect($browserDetect)
                -> insert();

        // create a new Hash ID from the ID
        $shortCode = Generator::HashId($url -> getId());

        // update the record
        $url -> setShortCode($shortCode) -> update();

        // check if by certain user or not
        if ($userId) {
            // create a user-url record as well
            $userUrl = (new Models\UserUrl)
                    -> setUserId($userId)
                    -> setUrlId($url -> getId())
                    -> insert();

            $url -> addAttribute('user_url', $userUrl);

            return $userUrl;
        } else {
            return $url;
        }
    }

}
