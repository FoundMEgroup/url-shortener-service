<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of User
 *
 * @author bertmaurau
 */
class User extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "users";
    // Define what the primary key is
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    const FILTERS = [];
    // Does the table have timestamps? (created_at, updated_at, deleted_at)
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [
        'first_name' => [true, 'string', 1, 128],
        'last_name'  => [true, 'string', 1, 128],
    ];
    // list of updatable fields
    const UPDATABLE = ['first_name' => '', 'last_name' => ''];

    /**
     * UID
     * @var string
     */
    public $uid;

    /**
     * First Name
     * @var string
     */
    public $first_name;

    /**
     * Last Name
     * @var string
     */
    public $last_name;

    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Password
     * @var string
     */
    protected $password;

    /**
     * Validated At
     * @var DateTime
     */
    protected $validated_at;

    /**
     * Validate the given password for requested User ID
     *
     * @param integer $userId The user ID
     * @param string $password The password hash
     *
     * @return User
     */
    public function validatePassword(string $password): User
    {

        // validate password
        if (!password_verify($password, $this -> getPassword())) {
            return false;
        }

        // verify legacy password to new password_hash options
        if (password_needs_rehash($this -> getPassword(), PASSWORD_DEFAULT, ['cost' => 11])) {
            // rehash/store plain-text password using new hash
            $newHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 11]);
            $this -> setPassword($newHash) -> update();
        }

        return true;
    }

    /**
     * Check for a valid login
     *
     * @param string $email The email address
     * @param string $password The password hash
     *
     * @return User
     */
    public static function validateLogin(string $email, string $password): User
    {
        return (new self) -> findBy(['email' => $email, 'password' => $password], $take = 1);
    }

    /**
     * Get UID
     *
     * @return string UID
     */
    public function getUid(): string
    {
        return $this -> uid;
    }

    /**
     * Get First Name
     *
     * @return string First Name
     */
    public function getFirstName(): string
    {
        return $this -> first_name;
    }

    /**
     * Get Last Name
     *
     * @return string Last Name
     */
    public function getLastName(): string
    {
        return $this -> last_name;
    }

    /**
     * Get Email
     *
     * @return string Email
     */
    public function getEmail(): string
    {
        return $this -> email;
    }

    /**
     * Get Password
     *
     * @return string Password
     */
    public function getPassword(): string
    {
        return $this -> password;
    }

    /**
     * Get Validated At
     *
     * @return \DateTime Validated At
     */
    public function getValidatedAt(): \DateTime
    {
        return $this -> validated_at;
    }

    /**
     * Set UID
     *
     * @param string $uid uid
     *
     * @return $this
     */
    public function setUid(string $uid): User
    {
        $this -> uid = $uid;

        return $this;
    }

    /**
     * Set First Name
     *
     * @param string $firstName first_name
     *
     * @return $this
     */
    public function setFirstName(string $firstName = null): User
    {
        $this -> first_name = $firstName;

        return $this;
    }

    /**
     * Set Last Name
     *
     * @param string $lastName last_name
     *
     * @return $this
     */
    public function setLast_name(string $lastName = null): User
    {
        $this -> last_name = $lastName;

        return $this;
    }

    /**
     * Set Email
     *
     * @param string $email email
     *
     * @return $this
     */
    public function setEmail(string $email): User
    {
        $this -> email = $email;

        return $this;
    }

    /**
     * Set Password
     *
     * @param string $password password
     *
     * @return $this
     */
    public function setPassword(string $password): User
    {
        $this -> password = $password;

        return $this;
    }

    /**
     * Set Validated At
     *
     * @param \DateTime $validatedAt validated_at
     *
     * @return $this
     */
    public function setValidatedAt(\DateTime $validatedAt = null): User
    {
        $this -> validated_at = $validatedAt;

        return $this;
    }

}
