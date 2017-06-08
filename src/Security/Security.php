<?php

namespace Shulha\Framework\Security;

use Shulha\Framework\DI\Injector;
use Shulha\Framework\DI\Service;

/**
 * Class Security
 * @package Shulha\Framework\Security
 */
class Security
{
    /**
     * @var Session instance
     */
    protected $session;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var User
     */
    protected static $user = null;

    /**
     * Security constructor.
     */
    public function __construct()
    {
        $this->config = Injector::$config;
        $this->session = Service::get('injector')->share('Shulha\Framework\Session\Session');
        $this->session = Service::get('injector')->make('Shulha\Framework\Session\Session');
        $this->getUser();
    }

    /**
     * Get current user object
     *
     * @return UserContract
     */
    public function getUser(): UserContract
    {
        if (empty(self::$user)) {
            $user_id = $this->session->user_id;
            $user = Service::get('injector')->make('Shulha\Framework\Security\UserContract');

            if ($user_id) {
                $user = $user->find($user_id);
            }

            self::$user = $user;
        }

        return self::$user;
    }

    /**
     * Check current authorization status
     *
     * @return bool
     */
    public function checkAuth(): bool
    {
        return !self::$user->isGuest();
    }

    /**
     * Authorize user
     * @param UserContract $user
     */
    public function authorize(UserContract $user)
    {
        $this->session->user_id = $user->id;
        $this->session->role = $user->getRoles();
        $this->getUser();
    }

    /**
     * Call statically
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = Service::get('injector')->make('Shulha\Framework\Security\Security');

        return call_user_func_array([$instance, $name], $arguments);
    }
}