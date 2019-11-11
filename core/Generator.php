<?php

namespace BertMaurau\URLShortener\Core;

use Hashids\Hashids;

/**
 * Description of Generator
 *
 * Handles everything concerning the generation of random strings etc.
 *
 * @author Bert Maurau
 */
class Generator
{

    /**
     * Generate a random string with requested length
     *
     * @param int $length
     *
     * @return string
     */
    private static function Generate(int $length)
    {
        return substr(md5(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32)))), 0, $length);
    }

    /**
     * Generate a UID
     *
     * @return string
     */
    public static function Uid()
    {
        return self::Generate(32);
    }

    /**
     * Generate a URL Hash ID
     *
     * @return string
     */
    public static function HashId($id)
    {
        $hashids = new Hashids(Config::getInstance() -> HashId() -> seed, (int) Config::getInstance() -> HashId() -> length, Config::getInstance() -> HashId() -> alphabet);

        return $hashids -> encode($id);
    }

}
