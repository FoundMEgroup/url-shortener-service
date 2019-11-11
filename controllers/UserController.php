<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Config AS Config;
use BertMaurau\URLShortener\Models AS Models;
use BertMaurau\URLShortener\Modules AS Modules;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "User";

    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $user = (new Models\User) -> findBy(['email' => $filteredInput['email']], 1);
        if (!$user) {
            return Core\Output::NotFound($response, 'No user found for given email address `' . $filteredInput['email'] . '`');
        } else {
            if (!$user -> validatePassword($filteredInput['password'])) {
                return Core\Output::NotAuthorized($response, 'Incorrect password.');
            } else {

                $userAuthToken = Models\UserAuthToken::createForUser($user -> getId(), Core\Config::getInstance() -> API() -> env);

                // Add the generated token to the response
                $user -> addAttribute('access_token', $userAuthToken);

                return Core\Output::OK($response, $user);
            }
        }
    }

    /**
     * Show the currently authenticated user
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

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        return Core\Output::OK($response, $user);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'email', 'type' => Core\ValidatedRequest::TYPE_EMAIL, 'required' => true,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        $user = (new Models\User) -> findBy(['email' => $filteredInput['email']], 1);
        if ($user) {
            return Core\Output::NotFound($response, 'Record already exists for email address `' . $filteredInput['email'] . '`');
        }

        $passwordHash = password_hash($filteredInput['password'], PASSWORD_DEFAULT, ['cost' => 11]);

        $user = (new Models\User)
                -> setUid(Core\Generator::Uid())
                -> setEmail($filteredInput['email'])
                -> setPassword($passwordHash)
                -> insert();

        $userAuthToken = Models\UserAuthToken::createForUser($user -> getId(), Core\Config::getInstance() -> API() -> env);

        // Add the generated token to the response
        $user -> addAttribute('access_token', $userAuthToken);

        return Core\Output::OK($response, $user);
    }

    /**
     * Update the currently authenticated user
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
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'first_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'last_name', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => false,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // Get the POST body and filter out the non-updatables
        $postdata = (object) array_intersect_key($filteredInput, Models\User::UPDATABLE);

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }

        // map the values to the model
        $user -> map($postdata);

        // update the record
        $user -> update();

        // output the item with its updated values
        return Core\Output::OK($response, $user);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $userId = Core\Auth::getUserId();
        if (!$userId) {
            return Core\Output::NotAuthorized($response);
        }

        // define required arguments/values
        $validationFields = [
            ['method' => Core\ValidatedRequest::METHOD_POST, 'field' => 'password', 'type' => Core\ValidatedRequest::TYPE_STRING, 'required' => true,],
        ];

        $validatedRequest = Core\ValidatedRequest::validate($request, $response, $validationFields, $args);
        if (!$validatedRequest -> isValid()) {
            return $validatedRequest -> getOutput();
        }

        $filteredInput = $validatedRequest -> getFilteredInput();

        // get user resource
        $user = (new Models\User) -> getById($userId);
        if (!$user) {
            return Core\Output::ModelNotFound($response, 'User', $userId);
        }
        if (!$user -> validatePassword($filteredInput['password'])) {
            return Core\Output::NotAuthorized($response, 'Incorrect password.');
        }

        // map the values to the model
        $user -> map($postdata);

        // update the record
        $user -> update();

        // output the item with its updated values
        return Core\Output::OK($response, $user);
    }

}
