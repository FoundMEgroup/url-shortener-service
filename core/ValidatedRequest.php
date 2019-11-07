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
                case 'GET':
                    $value = filter_input(INPUT_GET, $validationField['field'], self::getFilterSanitizationType($validationField['type']));
                    break;
                case 'POST':
                    $value = filter_var($payload[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                    break;
                case 'ARG':
                    $value = filter_var($input[$validationField['field']], self::getFilterSanitizationType($validationField['type']));
                    break;
                default:
                    break;
            }

            if (!$value && $validationField['required']) {
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
     * @return Output The Output response
     */
    public function getOutput(): Output
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
            case 'string':
                return FILTER_SANITIZE_STRING;
            case 'integer':
                return FILTER_SANITIZE_NUMBER_INT;
            case 'float':
                return FILTER_SANITIZE_NUMBER_FLOAT;
            case 'email':
                return FILTER_SANITIZE_EMAIL;
            case 'boolean':
                return FILTER_VALIDATE_BOOLEAN;
            default:
                return 0;
        }
    }

}
