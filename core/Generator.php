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

    /**
     * Generate a GUID v4
     *
     * @param mixed $data data
     *
     * @return string|null
     */
    public static function GUIDv4(mixed $data = null): ?string
    {

        $data = $data ?? random_bytes(16);

        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

}
