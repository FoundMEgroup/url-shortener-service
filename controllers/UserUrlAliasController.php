<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Config AS Config;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserUrlAliasController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "UserUrlAlias";

    /**
     * Create a new URL Alias
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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'userUrlId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'alias', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
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

        $urlAlias = (new Models\UrlAlias) -> findBy(['url_id' => $userUrl -> getUrlId(), 'alias' => $filteredInput['alias']], $take = 1);
        if ($urlAlias) {
            return Core\Output::Conflict($response, "Alias '{$urlAlias -> getAlias()}' already exists for this URL.");
        }

        $urlAlias = (new Models\UrlAlias) -> setUrlId($userUrl -> getUrlId()) -> setAlias($filteredInput['alias']) -> insert();

        $urlAlias -> addAttribute('url', $url);

        return Core\Output::OK($response, $urlAlias);
    }

    /**
     * Update a specific URL Alias
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
            ['method' => Core\ValidatedRequest::METHOD_ARG, 'field' => 'aliasId', 'type' => Core\ValidatedRequest::TYPE_INTEGER, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'alias', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
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

        $urlAlias = (new Models\UrlAlias) -> getById($filteredInput['aliasId']);
        if (!$urlAlias) {
            return Core\Output::ModelNotFound($response, 'UrlAlias', $filteredInput['aliasId']);
        }

        $urlAlias
                -> setAlias($filteredInput['alias'])
                -> update();

        return Core\Output::OK($response, $urlAlias);
    }

    /**
     * Delete a specific URL Alias
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

        $urlAlias = (new Models\UrlAlias) -> getById($filteredInput['aliasId']);
        if (!$urlAlias) {
            return Core\Output::ModelNotFound($response, 'UrlAlias', $filteredInput['aliasId']);
        }

        $urlAlias -> delete();


        return Core\Output::OK($response, $urlAlias);
    }

}
