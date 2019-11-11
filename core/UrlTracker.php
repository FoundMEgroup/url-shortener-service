<?php

namespace BertMaurau\URLShortener\Core;

use BertMaurau\URLShortener\Models AS Models;

/**
 * Description of UrlTracker
 *
 * @author bertmaurau
 */
class UrlTracker
{

    const TYPE_URL = 'url';
    const TYPE_URL_ALIAS = 'url_alias';

    /**
     * Track the request
     *
     * @param string $type The model type
     * @param int $modelId The ID of the model
     *
     * @return void
     */
    public static function track(string $type, int $modelId)
    {

        if ($type === self::TYPE_URL) {
            $trackRequest = (new Models\UrlRequest)
                    -> setUrlId($modelId);
        } else if ($type === self::TYPE_URL_ALIAS) {
            $trackRequest = (new Models\UrlAliasRequest)
                    -> setUrlAliasId($modelId);
        }

        $trackRequest -> setRemoteAddress(Auth::getRemoteAddress()) -> insert();

        return;
    }

}
