<?php

namespace BertMaurau\URLShortener\Models;

/**
 * Description of UserAuthToken
 *
 * @author bertmaurau
 */
class UserAuthToken extends BaseModel
{

    /**
     * User ID
     * @var int
     */
    private $userId;

    /**
     * UID
     * @var string
     */
    private $uid;

    /**
     * Environment
     * @var string
     */
    private $env;

    /**
     * Expires At
     * @var DateTime
     */
    private $expiresAt;

    /**
     * Is Active
     * @var boolean
     */
    private $isActive;

    /**
     * Check if token is expired
     *
     * @return boolean Expired
     */
    public function isExpired()
    {
        return (new \DateTime() > $this -> getExpiresAt());
    }

    /**
     * Check if token is disabled
     *
     * @return boolean Disabled
     */
    public function isDisabled()
    {
        return !$this -> getIsActive();
    }

    /**
     * Check if token is active
     *
     * @return boolean Active
     */
    public function isActive()
    {
        return $this -> getIsActive();
    }

    /**
     * Get User ID
     * @return int The User ID
     */
    public function getUserId()
    {
        return $this -> userId;
    }

    /**
     * Get UID
     *
     * @return string The UID
     */
    public function getUid()
    {
        return $this -> uid;
    }

    /**
     * Get Env
     *
     * @return string The Environment
     */
    public function getEnv()
    {
        return $this -> env;
    }

    /**
     * Get Expires At
     *
     * @return DateTime The Expires At datetime
     */
    public function getExpiresAt()
    {
        return $this -> expiresAt;
    }

    /**
     * Get Is Active
     *
     * @return boolean The Active flag
     */
    public function getIsActive()
    {
        return $this -> isActive;
    }

    /**
     * Set User ID
     *
     * @param int $userId user_id
     *
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this -> userId = $userId;
        return $this;
    }

    /**
     * Set UID
     *
     * @param string $uid uid
     *
     * @return $this
     */
    public function setUid(string $uid)
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
    public function setEnv(string $env)
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
    public function setExpiresAt(string $expiresAt)
    {
        // parse timestamp to a DateTime instance
        if ($expiresAt) {
            try {
                $dt = new \DateTime($expiresAt);
            } catch (\Exception $ex) {
                throw new \Exception("Could not parse given timestamp (UserAuthToken::expiresAt).");
            }
            $this -> expiresAt = $dt;
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
    public function setIsActive($isActive)
    {
        $this -> isActive = (boolean) $isActive;

        return $this;
    }

}
