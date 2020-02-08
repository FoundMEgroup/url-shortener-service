<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserUrlController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "UserUrl";

    /**
     * Get all resources
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'take', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'skip', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'short_code', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'url', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_GET, 'field' => 'browser_detect', 'type' => Core\ValidatedRequest::TYPE_BOOLEAN, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }
        $filteredInput = $validatedRequest -> getFilteredInput();

        // Maybe handle the GET query string better.
        // Do what you want here.
        // Non-allowed filters will eventually get filtered out.
        $filter = array_intersect_key($filteredInput, Models\Url::FILTERS);

        $models = (new Models\Url()) -> findByUserId($userId, $filter, $filteredInput['take'], $filteredInput['skip']);


        // This will return an array-list of mapped object items.
        return Core\Output::OK($response, $models);
    }

    /**
     * Show a specific user-created URL
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userUrlId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $userUrl = (new Models\UserUrl) -> getById($filteredInput['userUrlId']);
        if (!$userUrl) {
            return Core\Output::ModelNotFound($response, 'UserUrl', $filteredInput['userUrlId']);
        }
        if ($userUrl -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $url = (new Models\Url) -> getById($userUrl -> getUrlId());
        if (!$url) {
            return Core\Output::ModelNotFound($response, 'Url', $userUrl -> getUrlId());
        }

        $url -> addAttribute('user_url', $userUrl);

        return Core\Output::OK($response, $url);
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
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'url', 'type' => Core\ValidatedRequest::TYPE_URL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browser_detect', 'type' => Core\ValidatedRequest::TYPE_BOOLEAN, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // since it's public, no need to check for existing URL
        $url = Core\UrlShortener::create($filteredInput['url'], $userId, $filteredInput['browser_detect'] ?? false);

        return Core\Output::OK($response, $url);
    }

    /**
     * Update a specific URL
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userUrlId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'url', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'browser_detect', 'type' => Core\ValidatedRequest::TYPE_BOOLEAN, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();


        $userUrl = (new Models\UserUrl) -> getById($filteredInput['userUrlId']);
        if (!$userUrl) {
            return Core\Output::ModelNotFound($response, 'UserUrl', $filteredInput['userUrlId']);
        }
        if ($userUrl -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $url = (new Models\Url) -> getById($userUrl -> getUrlId());
        if (!$url) {
            return Core\Output::ModelNotFound($response, 'Url', $userUrl -> getUrlId());
        }

        $url
                -> setUrl($filteredInput['url'] ?? $url -> getUrl())
                -> setBrowserDetect($filteredInput['browser_detect'] ?? $url -> getBrowserDetect())
                -> update();

        $url -> addAttribute('user_url', $userUrl);

        return Core\Output::OK($response, $url);
    }

    public function overview(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userUrlId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $userUrl = (new Models\UserUrl) -> findBy(['id' => $filteredInput['userUrlId']], $take = 1);
        if (!$userUrl) {
            return Core\Output::ModelNotFound($response, 'UserUrl', $filteredInput['userUrlId']);
        }
        if ($userUrl -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $url = (new Models\Url) -> getById($userUrl -> getUrlId());
        $url -> addAttribute('user_url', $userUrl);

        $urlRequests = (new Models\UrlRequest) -> getLastRequestsForUrlId($userUrl -> getUrlId(), 25);
        $urlRequestsOverview = (new Models\UrlRequest) -> getOverviewForUrlId($userUrl -> getUrlId());
        $urlAliasses = (new Models\UrlAlias) -> findBy(['url_id' => $userUrl -> getUrlId()], $take = 250);

        return Core\Output::OK($response, [
                    'url'              => $url,
                    'last_requests'    => $urlRequests,
                    'request_overview' => $urlRequestsOverview,
                    'aliasses'         => $urlAliasses,
        ]);
    }

    /**
     * Delete a specific URL
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     *
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userUrlId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'delete_full', 'type' => Core\ValidatedRequest::TYPE_BOOLEAN, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $userUrl = (new Models\UserUrl) -> findBy(['id' => $filteredInput['userUrlId']], $take = 1);
        if (!$userUrl) {
            return Core\Output::ModelNotFound($response, 'UserUrl', $filteredInput['userUrlId']);
        }
        if ($userUrl -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }


        if (isset($validationFields['delete_full']) && $validationFields['delete_full']) {
            // delete everything related to this user_url
            $userUrl -> deleteFull();
        } else {
            // delete just the link with the user's account
            $userUrl -> delete();
        }


        return Core\Output::OK($response, $userUrl);
    }

}
