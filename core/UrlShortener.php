<?php

namespace BertMaurau\URLShortener\Core;

use BertMaurau\URLShortener\Models AS Models;
use Hashids\Hashids;

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
     * @return Url|UserUrl
     */
    public static function create(string $extUrl, int $userId = null)
    {

        // create a new URL entry
        $url = (new Models\Url)
                -> setUrl($extUrl)
                -> insert();

        // create a new Hash ID from the ID
        $shortCode = self::generateHashId(
                        $url -> getId(), Core\Config::getInstance() -> HashId() -> seed, Core\Config::getInstance() -> HashId() -> length, Core\Config::getInstance() -> HashId() -> alphabet);

        // update the record
        $url -> setShortCode($shortCode) -> update();

        // check if by certain user or not
        if ($userId) {
            // create a user-url record as well
            $userUrl = (new Models\UserUrl)
                    -> setUserId($userId)
                    -> setUrlId($url -> getId())
                    -> insert();

            $userUrl -> addAttribute('url', $url);

            return $userUrl;
        } else {
            return $url;
        }
    }

    private static function generateHashId(int $id, string $salt, int $length = 12, string $alphabet = null)
    {
        $hashids = new Hashids($salt, $length, $alphabet);
        return $hashids -> encode($id);
    }

}
