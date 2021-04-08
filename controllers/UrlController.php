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
     * Go to URL by given code or alias
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return null
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // check for method/filter
        if ($code = Core\ValidatedRequest::filterInput(INPUT_GET, 'code')) {
            $fieldName = 'code';
            $fieldInput = Core\ValidatedRequest::METHOD_GET;
        } else if (isset($args['code']) && $code = Core\ValidatedRequest::filterVar($args['code'])) {
            $fieldName = 'code';
            $fieldInput = Core\ValidatedRequest::METHOD_ARG;
        } else {
            return Core\Output::MissingParameter($response, 'Missing URL parameter code.');
        }

        // define required arguments/values
        $validationFields = [
            ['method' => $fieldInput, 'field' => $fieldName, 'type' => Core\ValidatedRequest::TYPE_MIXED, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $url = (new Models\Url) -> findBy(['short_code' => $filteredInput['code']], $take = 1);
        if (!$url) {
            return Core\Output::ModelNotFound($response, 'Url', $filteredInput['code'], 'code');
        }

        // check if it's a bot/preview thingy
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        if (!Models\UrlRequest::isRequestFromBot($ua)) {
            $urlRequest = Models\UrlRequest::init($url -> getId());
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
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'force_new', 'type' => Core\ValidatedRequest::TYPE_BOOLEAN, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();


        if (isset($filteredInput['force_new']) && $filteredInput['force_new']) {
            $url = null;
        } else {
            // check if exact URL is already shortened for this user
            $url = (new Models\Url) -> findBy(['url' => $filteredInput['url'], 'leadcamp_user_id' => Core\Auth::getUserId()], $take = 1);
        }

        if (!$url) {
            // since it's public, no need to check for existing URL
            $url = Core\UrlShortener::create($filteredInput['url'], Core\Auth::getUserId());
        }

        // add the final url
        $url -> addAttribute('target_url_short', Core\Config::getInstance() -> API() -> baseUrlShort . '/' . $url -> getShortCode());
        $url -> addAttribute('target_url', Core\Config::getInstance() -> API() -> baseUrl . '/' . $url -> getShortCode());

        return Core\Output::OK($response, $url);
    }

}
