<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UserAuthToken
 *
 * @author bertmaurau
 */
class UserAuthToken extends BaseModel
{

    // The name of the database table
    const DB_TABLE = "user_auth_tokens";
    // Define what the primary key is
    const PRIMARY_KEY = "id";
    // Allowed filter params for the get requests
    const FILTERS = [];
    // Does the table have timestamps? (created_at, updated_at, deleted_at)
    const TIMESTAMPS = true;
    // Use soft deletes?
    const SOFT_DELETES = true;
    // Validation rules
    const VALIDATION = [];
    // list of updatable fields
    const UPDATABLE = ['is_active' => ''];

    /**
     * User ID
     * @var int
     */
    protected $user_id;

    /**
     * UID
     * @var string
     */
    public $uid;

    /**
     * Environment
     * @var string
     */
    public $env;

    /**
     * Expires At
     * @var DateTime
     */
    public $expires_at;

    /**
     * Is Active
     * @var bool
     */
    public $is_active;

    /**
     * Check if token is expired
     *
     * @return bool Expired
     */
    public function isExpired(): bool
    {
        return (new \DateTime() > $this -> getExpiresAt());
    }

    /**
     * Check if token is disabled
     *
     * @return bool Disabled
     */
    public function isDisabled(): bool
    {
        return !$this -> getIsActive();
    }

    /**
     * Check if token is active
     *
     * @return bool Active
     */
    public function isActive(): bool
    {
        return $this -> getIsActive();
    }

    /**
     * Register a new Auth Token for given User ID
     *
     * @param int $userId User ID
     * @param string $env Env
     *
     * @return UserAuthToken
     */
    public static function createForUser(int $userId, string $env): UserAuthToken
    {
        return (new self)
                        -> setUserId($userId)
                        -> setIsActive(true)
                        -> setEnv($env)
                        -> setUid(self::generateUid($userId, $env))
                        -> insert();
    }

    /**
     * Generate UID
     *
     * @param int $userId User ID
     * @param string $env Env
     *
     * @return string UID
     */
    private static function generateUid(int $userId, string $env): string
    {
        return md5($userId . $env . time());
    }

    /**
     * Get User ID
     * @return int User ID
     */
    public function getUserId(): int
    {
        return $this -> user_id;
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
     * Get Env
     *
     * @return string Env
     */
    public function getEnv(): string
    {
        return $this -> env;
    }

    /**
     * Get Expires At
     *
     * @return DateTime Expires At
     */
    public function getExpiresAt(): \DateTime
    {
        return $this -> expires_at;
    }

    /**
     * Get Is Active
     *
     * @return bool Is Active
     */
    public function getIsActive(): bool
    {
        return $this -> is_active;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return $this
     */
    public function setUserId(int $userId): UserAuthToken
    {
        $this -> user_id = $userId;

        return $this;
    }

    /**
     * Set UID
     *
     * @param string $uid uid
     *
     * @return $this
     */
    public function setUid(string $uid): UserAuthToken
    {
        $this -> uid = $uid;

        return $this;
    }

    /**
     * Set Env
     *
     * @param string $env env
     *
     * @return $this
     */
    public function setEnv(string $env): UserAuthToken
    {
        $this -> env = $env;

        return $this;
    }

    /**
     * Set Expires At
     *
     * @param string $expiresAt expires_at
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function setExpiresAt(string $expiresAt): UserAuthToken
    {
        // parse timestamp to a DateTime instance
        if ($expiresAt) {
            try {
                $dt = new \DateTime($expiresAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (UserAuthToken::expiresAt).");
            }
            $this -> expires_at = $dt;
        }

        return $this;
    }

    /**
     * Set Is Active
     *
     * @param any $isActive is_active
     *
     * @return $this
     */
    public function setIsActive($isActive): UserAuthToken
    {
        $this -> is_active = (boolean) $isActive;

        return $this;
    }

}
