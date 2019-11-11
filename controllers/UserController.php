<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Config AS Config;
use BertMaurau\URLShortener\Models AS Models;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends BaseController
{

    // Set the current ModelName that will be used (main)
    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "User";

    public function postLogin(ServerRequestInterface $request, ResponseInterface $response, array $args)
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
            if (!$user -> validateLogin($filteredInput['password'])) {
                return Core\Output::NotAuthorized($response, 'Incorrect password.');
            } else {

                $userAuthToken = Models\UserAuthToken::createForUser($user -> getId, Core\Config::getInstance() -> API() -> env);

                // generate a new JWT Token
                $token = \JWT::encode([
                            'env'     => Core\Config::getInstance() -> API() -> env,
                            'userId'  => $user -> getId(),
                            'tokenId' => $userAuthToken -> getUid(),
                                ], Core\Config::getInstance() -> Salts() -> token);

                // Add the generated token to the response
                $user -> addAttribute('access_token', $token);

                return Core\Output::OK($response, $user);
            }
        }
    }

}
