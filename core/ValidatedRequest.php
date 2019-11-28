<?php

namespace BertMaurau\URLShortener\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ValidatedRequest
 *
 * @author bertmaurau
 */
class ValidatedRequest
{

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_URL = 'url';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_EMAIL = 'email';
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    const METHOD_ARG = 'ARG';

    /**
     * Instace
     * @var ValidatedRequest
     */
    private $instance;

    /**
     * Filtered Input
     * @var array
     */
    private $filteredInput = [];

    /**
     * Is Valid
     * @var bool
     */
    private $isValid = true;

    /**
     * Output
     * @var Output
     */
    private $output;

    /**
     * Validate Request
     * @param ServerRequestInterface $request The RequestInterface
     * @param ResponseInterface $response The ResponseInterface
     * @param array $validationFields List of fields needed
     * @param array $input List of fields provided
     * @return ValidatedRequest
     */
    public static function validate(ServerRequestInterface $request, ResponseInterface $response, array $validationFields = [], array $input = []): ValidatedRequest
    {
        $instance = (new self);

        // Get the POST body
        $payload = json_decode($request -> getBody(), true);

        // iterate the validation fields
        foreach ($validationFields as $key => $validationField) {

            switch ($validationField['method']) {
                case self::METHOD_GET:
                    if (!isset($_GET[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    }
                    $value = filter_input(INPUT_GET, $validationField['field'], self::getFilterSanitizationType($validationField['type']));
                    break;
                case self::METHOD_POST:
                    if (!isset($payload[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    }
                    $value = filter_var($payload[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                    break;
                case self::METHOD_ARG:
                    if (!isset($input[$validationField['field']]) && $validationField['required']) {
                        $instance -> isValid = false;
                        $instance -> output = Output::MissingParameter($response, $validationField['field']);
                        return $instance;
                    }
                    $value = filter_var($input[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                    break;
                default:
                    break;
            }

            if (!$value) {
                $instance -> isValid = false;
                $instance -> output = Output::InvalidParameter($response, $validationField['field']);
                return $instance;
            } else {
                $instance -> filteredInput[$validationField['field']] = $value;
            }
        }

        return $instance;
    }

    /**
     * Custom function for filter_input to fix issues with FastCGI and server/environment vars
     *
     * @source https://stackoverflow.com/a/36205923/3347968
     *
     * @param int $type
     * @param string $variable_name
     * @param int $filter
     * @param type $options
     *
     * @return mixed
     */
    public static function filterInput(int $type, string $variable_name, int $filter = FILTER_DEFAULT, $options = NULL)
    {
        $checkTypes = [
            INPUT_GET,
            INPUT_POST,
            INPUT_COOKIE
        ];

        if ($options === NULL) {
            // No idea if this should be here or not
            // Maybe someone could let me know if this should be removed?
            $options = FILTER_NULL_ON_FAILURE;
        }

        if (in_array($type, $checkTypes) || filter_has_var($type, $variable_name)) {
            return filter_input($type, $variable_name, $filter, $options);
        } else if ($type == INPUT_SERVER && isset($_SERVER[$variable_name])) {
            return filter_var($_SERVER[$variable_name], $filter, $options);
        } else if ($type == INPUT_ENV && isset($_ENV[$variable_name])) {
            return filter_var($_ENV[$variable_name], $filter, $options);
        } else {
            return NULL;
        }
    }

    /**
     * Return Is valid
     *
     * @return bool Valid
     */
    public function isValid(): bool
    {
        return $this -> isValid;
    }

    /**
     * Get Filtered Input
     *
     * @return array The filtered Input
     */
    public function getFilteredInput(): array
    {
        return $this -> filteredInput;
    }

    /**
     * Get Output
     *
     * @return ResponseInterface The Output response
     */
    public function getOutput(): ResponseInterface
    {
        return $this -> output;
    }

    /**
     * Get the Sanitization Type for given variable type
     *
     * @param string $type The type of variable
     *
     * @return int The number of the sanitization type
     */
    private static function getFilterSanitizationType(string $type): int
    {
        switch ($type) {
            case self::TYPE_STRING:
                return FILTER_SANITIZE_STRING;
            case self::TYPE_INTEGER:
                return FILTER_SANITIZE_NUMBER_INT;
            case self::TYPE_FLOAT:
                return FILTER_SANITIZE_NUMBER_FLOAT;
            case self::TYPE_EMAIL:
                return FILTER_SANITIZE_EMAIL;
            case self::TYPE_BOOLEAN:
                return FILTER_VALIDATE_BOOLEAN;
            case self::TYPE_URL:
                return FILTER_VALIDATE_URL;
            default:
                return 0;
        }
    }

}
