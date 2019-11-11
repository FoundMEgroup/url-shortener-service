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

    /**
     * Get the URL by given code
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return null
     */
    public function getByUrlCode(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'urlCode', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $url = (new Models\Url) -> findBy(['short_code' => $filteredInput['urlCode']], $take = 1);
        if (!$url) {
            return Core\Output::ModelNotFound($response, 'Url', $filteredInput['urlCode'], 'c');
        }

        // do tracker magic
        Core\UrlTracker::track(Core\UrlTracker::TYPE_URL, $url -> getId());

        // url found, code exits.. do redirecting magic
        $url -> redirectToUrl();
    }

    /**
     * Get the URL by given alias
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return null
     */
    public function getByUrlAlias(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'urlAlias', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $urlAlias = (new Models\UrlAlias) -> findBy(['alias' => $filteredInput['urlAlias']], $take = 1);
        if (!$urlAlias) {
            return Core\Output::ModelNotFound($response, 'UrlAlias', $filteredInput['urlAlias'], 'a');
        }

        // do tracker magic
        Core\UrlTracker::track(Core\UrlTracker::TYPE_URL_ALIAS, $urlAlias -> getId());

        // get the URL for given alias
        $url = (new Models\Url) -> getById($urlAlias -> getUrlId());
        if (!$url) {
            return Core\Output::NotFound($response, 'URL associated with `' . $filteredInput['urlAlias'] . '` not found.');
        }

        // url found, code exits.. do redirecting magic
        $url -> redirectToUrl();
    }

    /**
     * Create a new anonymous URL
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'url', 'type' => Core\ValidatedRequest::TYPE_URL, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // since it's public, no need to check for existing URL
        $url = Core\UrlShortener::create($filteredInput['url']);

        return Core\Output::OK($response, $url);
    }

}
