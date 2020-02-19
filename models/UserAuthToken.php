<?php

namespace BertMaurau\URLShortener\Models;

use BertMaurau\URLShortener\Core AS Core;
use BertMaurau\URLShortener\Modules AS Modules;

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
     * Is Destroyed
     * @var bool
     */
    public $is_destroyed;

    /**
     * Check if token is valid (based on all conditions)
     *
     * @return bool Valid
     */
    public function isValid(): bool
    {
        return ($this -> isActive() && !$this -> isDestroyed() && !$this -> isDisabled() && !$this -> isExpired());
    }

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
     * Check if token is destroyed
     *
     * @return bool Destroyed
     */
    public function isDestroyed(): bool
    {
        return $this -> getIsDestroyed();
    }

    /**
     * Register a new Auth Token for given User ID
     *
     * @param int $userId User ID
     * @param string $env Env
     *
     * @return string
     */
    public static function createForUser(int $userId, string $env): string
    {
        $newDate = date("Y-m-d H:i:s", strtotime("+1 month"));

        $userAuthToken = (new self)
                -> setUserId($userId)
                -> setIsActive(true)
                -> setIsDestroyed(false)
                -> setEnv($env)
                -> setUid(Core\Generator::Uid())
                -> setExpiresAt($newDate)
                -> insert();

        // generate a new JWT Token
        return Modules\JWT::encode(
                        [
                            'env'      => Core\Config::getInstance() -> API() -> env,
                            'userId'   => $userId,
                            'tokenUid' => $userAuthToken -> getUid(),
                        ], Core\Config::getInstance() -> Salts() -> token);
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
     * @return \DateTime Expires At
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
     * Get Is Destroyed
     *
     * @return bool Is Destroyed
     */
    public function getIsDestroyed(): bool
    {
        return $this -> is_destroyed;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
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
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
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
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
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
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
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
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
     */
    public function setIsActive($isActive): UserAuthToken
    {
        $this -> is_active = (boolean) $isActive;

        return $this;
    }

    /**
     * Set Is Destroyed
     *
     * @param any $isDestroyed is_destroyed
     *
     * @return \BertMaurau\URLShortener\Models\UserAuthToken
     */
    public function setIsDestroyed($isDestroyed): UserAuthToken
    {
        $this -> is_destroyed = (boolean) $isDestroyed;

        return $this;
    }

}
