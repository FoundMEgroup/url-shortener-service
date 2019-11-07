<?php

namespace BertMaurau\URLShortener\Core;

/**
 * Description of Output
 *
 * Handles everything concerning the actual data output.
 *
 * @author Bert Maurau
 */
class Output
{

    // HTTP Codes
    const CODE_NOT_FOUND = 404;
    const CODE_CONFLICT = 406;
    const CODE_MISSING_PARAMETER = 406;
    const CODE_VALIDATION_FAILED = 406;
    const CODE_DISABLED = 409;
    const CODE_OK = 200;
    const CODE_NOT_AUTHORIZED = 401;
    const CODE_UNPROCESSABLE = 422;
    const CODE_ERROR = 500;
    const CODE_NO_CONTENT = 204;
    const CODE_LOCKED = 423;
    const CODE_BAD_REQUEST = 400;

    /**
     * JSON Output
     * @param Response $response
     * @param integer $code
     * @param any $data
     * @return Response
     */
    public static function JSON($response, $code, $data)
    {
        // maybe add some stuffs here
        // like extra validation, parameters, attributes, ..
        // actual output
        $response -> getBody()
                // write the output
                -> write(json_encode($data));
        // set the HTTP_CODE
        return $response -> withStatus($code);
    }

    /**
     * Init a new response object
     * @return \Zend\Diactoros\Response
     */
    public static function Clear()
    {
        return new \Zend\Diactoros\Response();
    }

    /**
     * Return OK status
     * @param Response $response
     * @param any $data
     * @return JSON Output
     */
    public static function OK($response, $data)
    {
        return self::JSON($response, self::CODE_OK, $data);
    }

    /**
     * Return Model Not Found status
     * @param Response $response
     * @param string $modelName
     * @param any $modelId
     * @return JSON Output
     */
    public static function ModelNotFound($response, $modelName, $modelId, $parameter = 'id')
    {
        return self::JSON($response, self::CODE_NOT_FOUND, array(
                    'code'    => self::CODE_NOT_FOUND,
                    'message' => $modelName . ' with `' . $parameter . '` ' . $modelId . ' not found!'));
    }

    /**
     * Return Not Found status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function NotFound($response, $message, $field = null, $value = null)
    {
        // return custom message
        if ($message && !$field && !$value) {
            return self::JSON($response, self::CODE_NOT_FOUND, array(
                        'code'    => self::CODE_NOT_FOUND,
                        'message' => $message));
        } else {
            return self::JSON($response, self::CODE_NOT_FOUND, array(
                        'code'    => self::CODE_NOT_FOUND,
                        'message' => "Model `$message` with `$value` as `$field` not found."));
        }
    }

    /**
     * Return Error status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function ServerError($response, $message)
    {
        return self::JSON($response, self::CODE_ERROR, array(
                    'code'    => self::CODE_ERROR,
                    'message' => $message));
    }

    /**
     * Return Missing Parameter status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function MissingParameter($response, $message)
    {
        return self::JSON($response, self::CODE_MISSING_PARAMETER, array(
                    'code'    => self::CODE_MISSING_PARAMETER,
                    'message' => "Missing payload property: `$message`."));
    }

    public static function Locked($response, $message)
    {
        return self::JSON($response, self::CODE_LOCKED, array(
                    'code'    => self::CODE_LOCKED,
                    'message' => $message));
    }

    /**
     * Return Conflict status (should be  not-acceptable)
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function Conflict($response, $message)
    {
        return self::JSON($response, self::CODE_CONFLICT, array(
                    'code'    => self::CODE_CONFLICT,
                    'message' => $message));
    }

    /**
     * Return No Content response
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function NoContent($response)
    {
        return self::JSON($response, self::CODE_NO_CONTENT, null);
    }

    /**
     * Return Disabled resource
     * @param type $response
     * @param type $message
     * @return type
     */
    public static function DisabledResource($response, $message)
    {
        return self::JSON($response, self::CODE_DISABLED, array(
                    'code'    => self::CODE_DISABLED,
                    'message' => $message));
    }

    /**
     * Return Unprocessable Entity status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function InvalidParameter($response, $parameter)
    {
        return self::JSON($response, self::CODE_UNPROCESSABLE, array(
                    'code'    => self::CODE_UNPROCESSABLE,
                    'message' => "Incorrect value/type for parameter `$parameter`."));
    }

    /**
     * Return Conflict status
     * @param Response $response
     * @param string $message
     * @return JSON Output
     */
    public static function ValidationFailed($response, $message)
    {
        return self::JSON($response, self::CODE_VALIDATION_FAILED, array(
                    'code'    => self::CODE_VALIDATION_FAILED,
                    'message' => $message));
    }

    /**
     * Return Missing Model Id status
     * @param Response $response
     * @return JSON Output
     */
    public static function MissingModelId($response)
    {
        return self::JSON($response, self::CODE_MISSING_PARAMETER, array(
                    'code'    => self::CODE_MISSING_PARAMETER,
                    'message' => "Missing ModelID {id} parameter!"));
    }

    /**
     * Return Not Authorized status
     * @param Response $response
     * @return JSON Output
     */
    public static function NotAuthorized($response, $message = null)
    {
        return self::JSON($response, self::CODE_NOT_AUTHORIZED, array(
                    'code'    => self::CODE_NOT_AUTHORIZED,
                    'message' => ($message) ?: 'You are not authorized to make this request.'));
    }

    /**
     * Return Bad Request status
     * @param Response $response
     * @return JSON Output
     */
    public static function BadRequest($response, $message = null)
    {
        return self::JSON($response, self::CODE_BAD_REQUEST, array(
                    'code'    => self::CODE_BAD_REQUEST,
                    'message' => ($message) ?: 'Bad request.'));
    }

}
