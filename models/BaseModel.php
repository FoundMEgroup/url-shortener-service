<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core AS Core;

/**
 * Description of BaseModel
 *
 * Handle every 'general' function that a Model that extends this class could use.
 *
 * - validate       Validate the values against the define rules
 * - map            Map the values to the model-properties
 * - addAttrbute    Add non-property values to the model as an attribute
 * - getById        Get the resource by the given ID
 * - findBy         Find a model by the given property
 * - getAll         Get all resources
 * - insert         Insert/create a new resource
 * - update         Update an existing resource
 * - delete         Delete an existing resource
 *
 * @author Bert Maurau
 */
class BaseModel
{

    // |------------------------------------------------------------------------
    // |  Model Configuration
    // |------------------------------------------------------------------------
    // Reference to the Database table (gets set within the Model class)
    const DB_TABLE = "";
    // Define what is the primary key
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    // Define on which fields the user can filter using GET params.
    const FILTERS = [];
    // Does the table have timestamps?
    // (created_at, updated_at, deleted_at)
    const TIMESTAMPS = false;
    // Use soft deletes?
    // (prevent actual record deletions, just update the deleted_at timestamp)
    const SOFT_DELETES = true;
    // Validation rules 'property' => [required, varType, min(length), max(length)]
    // (Ex. 'name' => [true, 'string', 1, 120])
    const VALIDATION = [];
    // list of updatable fields
    const UPDATABLE = [];
    // list of expendable properties
    const EXPANDABLE = [];

    // |------------------------------------------------------------------------
    // |  Properties
    // |------------------------------------------------------------------------

    /**
     * Attributes
     *
     * holds extra properties that are not model-specified-properties.
     * (items that don't have any setters)
     *
     * @var array
     */
    // s
    //
    public $attributes = array();

    /**
     * ID
     * @var DateTime
     */
    public $id;

    /**
     * Deleted At
     * @var DateTime
     */
    private $deleted_at;

    /**
     * Updated At
     * @var DateTime
     */
    public $updated_at;

    /**
     * Created At
     * @var DateTime
     */
    public $created_at;

    // |------------------------------------------------------------------------
    // |  Model Functions
    // |------------------------------------------------------------------------
    public function __construct(array $properties = [])
    {
        return $this -> map($properties);
    }

    /**
     * Validate the current model's properties using the validation rules
     *
     * @return array valid|reason
     */
    public function validate(): array
    {
        // check if there are validation rules
        if (count(static::VALIDATION) < 1) {
            // if not, just return valid
            return [true, 'OK'];
        }

        // get object properties
        $properties = get_object_vars($this);
        foreach ($properties as $property => $value) {
            // check with validation rule
            //
            // check if property has a specific rule defined
            if (isset(static::VALIDATION[$property])) {

                // check first if it's a required property
                $reqRequired = static::VALIDATION[$property][0];
                if (!$value && $reqRequired) {
                    return [false, "Missing required property " . $property];
                }

                // check for the variable type
                $reqVarType = static::VALIDATION[$property][1];
                $parVarType = gettype($value);
                if ($parVarType != $reqVarType) {
                    return [false, "Expected `" . $reqVarType . "`, got `" . $parVarType . "` for " . $property];
                }

                // if the property should be an email, do the necessary validations
                if ($reqVarType === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        return [false, $value . " is not a valid email address."];
                    }
                } else {
                    // check min length or value (only strings and integers)
                    $reqMin = static::VALIDATION[$property][2];
                    $reqMax = static::VALIDATION[$property][3];

                    if ($reqVarType == 'string') {
                        $parLength = strlen($value);
                        if ($parLength < $reqMin) {
                            return [false, $property . " requires a min-length of " . $reqMin . ". " . $parLength . " given."];
                        }
                        if ($parLength > $reqMax) {
                            return [false, $property . " requires a max-length of " . $reqMax . ". " . $parLength . " given."];
                        }
                    }
                    // check max length or value
                    if ($reqVarType == 'integer') {

                        if ($value < $reqMin) {
                            return [false, $property . " has a min-value of " . $reqMin . ". " . $parLength . " given."];
                        }
                        if ($value > $reqMax) {
                            return [false, $property . " has a max-value of " . $reqMax . ". " . $parLength . " given."];
                        }
                    }
                }
            }
        }

        // if all passed..
        return [true, 'OK'];
    }

    /**
     * Map the given properties to self, calling the setters.
     *
     * @param object $properties The list of properties to assign
     *
     * @return $this
     */
    public function map(array $properties = [])
    {
        if (isset($properties)) {
            // loop properties and attempt to call the setter
            foreach ($properties as $key => $value) {
                $setter = 'set' . str_replace('_', '', ucwords($key, '_'));
                // check if the setter exists and is callable
                if (is_callable(array($this, $setter))) {
                    // execute the setter
                    call_user_func(array($this, $setter), $value);
                } else {
                    // not a property, add to the attributes list
                    $this -> addAttribute($key, $value);
                }
            }
            return $this;
        }
    }

    /**
     * Add item as attribute
     *
     * @param string $property The name of the property
     * @param any $value The value
     *
     * @return $this
     */
    public function addAttribute(string $property, $value)
    {
        $this -> attributes[$property] = $value;

        return $this;
    }

    /**
     * Get model by ID
     *
     * @param int $id The ID
     *
     * @return $this
     */
    public function getById(int $id)
    {
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE `" . static::PRIMARY_KEY . "` = " . Core\Database::escape($id) . " "
                . ((static::SOFT_DELETES) ? " AND " . static::DB_TABLE . ".deleted_at IS NULL " : "")
                . "LIMIT 1;";
        $result = Core\Database::query($query);
        if ($result -> num_rows < 1) {
            return false;
        } else {
            // Create an object from the result
            return $this -> map($result -> fetch_assoc());
        }
    }

    /**
     * Get model by specific field
     *
     * @param array $fieldsWithValues List of fields to filter on
     * @param int $take Pagination take
     * @param int $skip Pagination skip
     *
     * @return $this
     */
    public function findBy(array $fieldsWithValues = array(), int $take = 120, int $skip = 0)
    {
        // check if the requested field exists for this model
        foreach ($fieldsWithValues as $field => $value) {
            if (!array_key_exists($field, get_object_vars($this))) {
                throw new \Exception("`" . $field . "` is not a recognized property.");
            } else {
                $conditions[] = "`" . $field . "` = '" . Core\Database::escape($value) . "'";
            }
        }

        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE 1=1 " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND " . static::DB_TABLE . ".deleted_at IS NULL " : "")
                . "LIMIT $take OFFSET $skip;";
        $result = Core\Database::query($query);
        if ($take && $take === 1) {
            if ($result -> num_rows < 1) {
                return false;
            } else {
                return $this -> map($result -> fetch_assoc());
            }
        } else {
            $response = [];
            while ($row = $result -> fetch_assoc()) {
                $response[] = (new $this) -> map($row);
            }
            return $response;
        }
    }

    /**
     * Get all Models (deprecated in favor of findBy)
     *
     * @param array $filter List of fields to filter on
     * @param int $take Pagination take
     * @param int $skip Pagination skip
     *
     * @return array
     */
    public function getAll(array $filter = [], int $take = 120, int $skip = 0): array
    {
        // Build WHERE conditions
        $conditions = array();
        foreach ($filter as $field => $value) {
            // check if the requested filter is allowed or available.
            if (in_array($field, static::FILTERS)) {
                $conditions[] = "`$field` LIKE '%$value%'";
            }
        }

        $response = [];
        $query = " SELECT * "
                . "FROM " . static::DB_TABLE . " "
                . "WHERE 1=1 " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " "
                . ((static::SOFT_DELETES) ? " AND " . static::DB_TABLE . ".deleted_at IS NULL " : "")
                . "LIMIT $take OFFSET $skip;";

        $result = Core\Database::query($query);
        while ($row = $result -> fetch_assoc()) {
            $resource = (new $this) -> map($row);
            $response[] = $resource;
        }
        return $response;
    }

    /**
     * Insert Model
     *
     * @return $this
     */
    public function insert()
    {

        // set timestamps
        if (static::TIMESTAMPS) {
            $this
                    -> setCreatedAt(date('Y-m-d H:i:s'))
                    -> setUpdatedAt(date('Y-m-d H:i:s'));
        }

        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'attributes' && !empty($value) && is_callable(array($this, 'get' . str_replace('_', '', ucwords($key, '_'))))) {
                $keys[] = '`' . Core\Database::escape($key) . '`';
                $values[] = Core\Database::escape($value);
            }
        }

        // Do more checks here for security..

        $query = " INSERT "
                . "INTO " . static::DB_TABLE . " (" . implode(",", $keys) . ") "
                . "VALUES ('" . implode("','", $values) . "');";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        $result = Core\Database::query($query);

        // Get the ID and add it to the model response
        $this -> id = Core\Database::getId();

        return $this;
    }

    /**
     * Update Model
     *
     * @return $this
     */
    public function update()
    {
        // set timestamps
        if (static::TIMESTAMPS) {
            $this
                    -> setUpdatedAt(date('Y-m-d H:i:s'));
        }

        // This should be modified to be a bit more secure, but normally public
        // properties will be filtered out, as well as the attributes property.
        foreach (get_object_vars($this) as $key => $value) {
            if ($key !== 'attributes' && !empty($value) && is_callable(array($this, 'get' . str_replace('_', '', ucwords($key, '_'))))) {
                $update[] = '`' . Core\Database::escape($key) . '`' . " = '" . Core\Database::escape($value) . "'";
            }
        }

        $query = " UPDATE " . static::DB_TABLE . " "
                . "SET " . implode(",", $update) . " "
                . "WHERE `" . static::PRIMARY_KEY . "` = " . Core\Database::escape($this -> getId()) . ";";

        // replace nulls with real nulls (for ex. deleted_at)
        $query = str_replace("'(null)'", "NULL", $query);

        $result = Core\Database::query($query);

        return $this;
    }

    /**
     * Delete Model
     *
     * $param bool $hardDelete Force hard-delete
     *
     * @return $this
     */
    public function delete($hardDelete = false)
    {

        // set timestamps
        if (static::TIMESTAMPS && static::SOFT_DELETES && !$hardDelete) {
            $this -> setDeletedAt(date('Y-m-d H:i:s')) -> update();
        } else {
            $query = " DELETE "
                    . "FROM " . static::DB_TABLE
                    . "WHERE `" . static::PRIMARY_KEY . "` = " . Core\Database::escape($this -> getId()) . ";";

            Core\Database::query($query);
        }

        return $this;
    }

    /**
     * Find & Delete Models
     *
     * @param array $fieldsWithValues List of fields to filter on
     * $param bool $hardDelete Force hard-delete
     *
     * @return $this
     */
    public function findAndDelete(array $fieldsWithValues = array(), $hardDelete = false)
    {

        if (count($fieldsWithValues) < 1) {
            throw new \Exception("You need to specify at least on field to find and delete by.");
        }

        // check if the requested field exists for this model
        foreach ($fieldsWithValues as $field => $value) {
            if (!array_key_exists($field, get_object_vars($this))) {
                throw new \Exception("`" . $field . "` is not a recognized property.");
            } else {
                $conditions[] = "`" . $field . "` = '" . Core\Database::escape($value) . "'";
            }
        }

        if (static::TIMESTAMPS && static::SOFT_DELETES && !$hardDelete) {
            $query = " UPDATE " . static::DB_TABLE . " "
                    . "SET deleted_at = NOW() "
                    . "WHERE 1=1 " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " ";
        } else {
            $query = " DELETE "
                    . "FROM " . static::DB_TABLE . " "
                    . "WHERE 1=1 " . ((count($conditions)) ? ' AND ' . implode(' AND ', $conditions) : "") . " ";
        }

        Core\Database::query($query);

        return $this;
    }

    /**
     * Get ID
     *
     * @return int ID
     */
    public function getId()
    {
        return $this -> id;
    }

    /**
     * Get Deleted At
     *
     * @return \DateTime Deleted At
     */
    public function getDeletedAt(): \DateTime
    {
        return $this -> deleted_at;
    }

    /**
     * Get Updated At
     *
     * @return \DateTime Updated At
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this -> updated_at;
    }

    /**
     * Get Created At
     *
     * @return \DateTime Created At
     */
    public function getCreatedAt(): \DateTime
    {
        return $this -> created_at;
    }

    /**
     * Set ID
     *
     * @param int $id id
     *
     * @return $this
     */
    public function setId(int $id)
    {
        $this -> id = $id;

        return $this;
    }

    /**
     * Set Deleted At
     *
     * @param string $deletedAt deleted_at
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setDeletedAt(string $deletedAt = null)
    {
        // parse timestamp to a DateTime instance
        if ($deletedAt) {
            try {
                $dt = new \DateTime($deletedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::deletedAt).");
            }
            $this -> deleted_at = $dt;
        }

        return $this;
    }

    /**
     * Set Updated At
     *
     * @param string $updatedAt updated_at
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setUpdatedAt(string $updatedAt)
    {
        // parse timestamp to a DateTime instance
        if ($updatedAt) {
            try {
                $dt = new \DateTime($updatedAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::updatedAt).");
            }
            $this -> updated_at = $dt;
        }

        return $this;
    }

    /**
     * Set Created At
     *
     * @param string $createdAt created_at
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setCreatedAt(string $createdAt)
    {
        // parse timestamp to a DateTime instance
        if ($createdAt) {
            try {
                $dt = new \DateTime($createdAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (BaseModel::createdAt).");
            }
            $this -> created_at = $dt;
        }

        return $this;
    }

}
