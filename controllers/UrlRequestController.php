<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UrlRequestController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "UrlRequest";

    /**
     * Store the received BrowserDetect
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function browserDetect(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'guid', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browserDetect', 'type' => Core\ValidatedRequest::TYPE_JSON, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $urlRequest = (new Models\UrlRequest) -> findBy(['guid' => $filteredInput['guid']], $take = 1);
        if (!$urlRequest) {
            return Core\Output::ModelNotFound($response, 'UrlRequest', $filteredInput['guid'], 'guid');
        }

        $urlRequest
                -> setBrowserDetect($filteredInput['browserDetect'])
                -> setLanguage($filteredInput['browserDetect']['browser']['language'] ?? null)
                -> setBrowser($filteredInput['browserDetect']['browser']['name'] ?? null)
                -> setPlatform($filteredInput['browserDetect']['browser']['platform'] ?? null)
                -> update();

        return Core\Output::NoContent($response);
    }

}
