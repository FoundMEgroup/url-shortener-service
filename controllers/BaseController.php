<?php

namespace BertMaurau\URLShortener\Controllers;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Models AS Models;

/**
 * Description of BaseController
 *
 * This handles the basic requests actions like
 *  - index  [GET]    (List all models)
 *  - show   [GET]    (List a specific model)
 *  - create [POST]   (Insert a new model)
 *  - update [PATCH]  (Update a specific model)
 *  - delete [DELETE] (Delete a specific model)
 *
 * @author Bert Maurau
 */
class BaseController
{

    const MODEL_NAME = 'BertMaurau\\URLShortener\\Models\\' . "";

    // Handle the main index GET
    public function index($request, $response, $args)
    {
        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        // Maybe handle the GET query string better.
        // Do what you want here.
        // Non-allowed filters will eventually get filtered out.
        $models = (new $modelClass()) -> getAll($_GET);

        // This will return an array-list of mapped object items.
        return Core\Output::OK($response, $models);
    }

    // Handle model request
    public function show($request, $response, $args)
    {
        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        // Check for the given modelId. (validate this more if you'd like)
        if (!isset($args['id'])) {
            // Return the defined output for a missing ID attribute.
            // Defined within the app/Output file.
            return Core\Output::MissingModelId($response);
        }
        // check if integer
        if (filter_var($args['id'], FILTER_VALIDATE_INT) === false) {
            return Core\Output::InvalidParameter($response, 'Value `' . $args['id'] . ' is not a valid Resource-ID (number|integer).');
        }

        // set the ID
        $modelId = $args['id'];

        // Init the model and get the resource by the given ID.
        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelName, $modelId);
        }

        // Output the object item.
        return Core\Output::OK($response, $model);
    }

    // Handle the create request
    public function create($request, $response, $args)
    {
        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        // Get the POST body and filter out the non-updatables
        $postdata = (object) array_intersect_key(json_decode($request -> getBody(), true), $modelClass::UPDATABLE);

        // Map the POST values to the model
        // Non-model properties will be put under `attributes` and will be skipped
        // when inserting the record.
        $model -> map($postdata);

        // apply validation rules to the mapped object before inserting
        if (!$validator[0] = $model -> validate()) {
            // If any validation rule failed. Return the defined output.
            return Core\Output::ValidationFailed($response, $validator[1]);
        }

        // Insert the model into the DB
        $model -> insert();

        // Return the newly created model with the generated ID
        return Core\Output::OK($response, $model);
    }

    // Handle an update request
    public function update($request, $response, $args)
    {
        // Get the current ModelName to init a class.
        $modelClass = static::MODEL_NAME;

        // Check for the given modelId. (validate this more if you'd like)
        if (!isset($args['id'])) {
            // Return the defined output for a missing ID attribute.
            // Defined within the app/Output file.
            return Core\Output::MissingModelId($response);
        }

        // check if integer
        if (filter_var($args['id'], FILTER_VALIDATE_INT) === false) {
            return Core\Output::InvalidParameter($response, 'Value `' . $args['id'] . ' is not a valid Resource-ID (number|integer).');
        }

        // set the ID
        $modelId = $args['id'];

        // Get the POST body and filter out the non-updatables
        $postdata = (object) array_intersect_key(json_decode($request -> getBody(), true), $modelClass::UPDATABLE);

        // Get the model by the ID first to get the current values.
        // This is optional, but will allow to return the entire item with its
        // values instead of empty/null/missing properties.
        // This will also, ofcourse, check if the resource exists.
        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelClass, $modelId);
        }

        // Map the POST values to the model
        $model -> map($postdata);

        // apply validation rules to the mapped object before inserting
        if (!$validator[0] = $model -> validate()) {
            // If any validation rule failed. Return the defined output.
            return Core\Output::ValidationFailed($response, $validator[1]);
        }

        // Update the record
        $model -> update();

        // Output the item with its updated values
        return Core\Output::OK($response, $model);
    }

    // Handle a delete request
    public function delete($request, $response, $args)
    {
        // Get the current ModelName to init a class.

        $modelClass = static::MODEL_NAME;

        // Check for the given modelId. (validate this more if you'd like)
        if (!isset($args['id'])) {
            // Return the defined output for a missing ID attribute.
            // Defined within the app/Output file.
            return Core\Output::MissingModelId($response);
        }

        // Set the ID
        $modelId = $args['id'];

        // Get the model by the ID first to check if it exists.
        $model = (new $modelClass()) -> getById($modelId);
        if (!$model) {
            // Return the defined 404 output.
            return Core\Output::ModelNotFound($response, $modelClass, $modelId);
        }

        // Delete the record
        $model -> delete();

        // Output the deleted model (with its values)
        return Core\Output::OK($response, $model);
    }

}
