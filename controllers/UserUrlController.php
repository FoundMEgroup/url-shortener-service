<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Config AS Config;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserUrlController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "UserUrl";

    /**
     * List all user-created URLs
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

        $userUrls = (new Models\UserUrl) -> getOverviewByUserId($userId);

        return Core\Output::OK($response, $userUrls);
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

        $userUrl = (new Models\UserUrl) -> findBy(['id' => $filteredInput['userUrlId']], $take = 1);
        if (!$userUrl) {
            return Core\Output::ModelNotFound($response, 'UserUrl', $filteredInput['userUrlId']);
        }
        if ($userUrl -> getUserId() !== $userId) {
            return Core\Output::NotAuthorized($response);
        }

        $userUrl = $userUrl -> getOverview();

        return Core\Output::OK($response, $userUrl);
    }

    /**
     * Create a new URL
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
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // since it's public, no need to check for existing URL
        $url = Core\UrlShortener::create($filteredInput['url'], $userId);

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

        return Core\Output::OK($response, $userUrl);
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
