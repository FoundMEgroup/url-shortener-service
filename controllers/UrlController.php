<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Config AS Config;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UrlController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "Url";

    public function getByUrlCode(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function getByUrlAlias(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        // define required arguments/values
        $validationFields = [
            [
                'method'   => 'POST', 'field'    => 'url', 'type'     => 'url', 'required' => true,
            ],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $url = Core\UrlShortener::create($filteredInput['url']);

        return Core\Output::OK($response, $url);
    }

}
