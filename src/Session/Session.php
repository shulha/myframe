<?php

namespace Shulha\Framework\Session;

/**
 * Class Session
 * Simple example of the session class
 *
 * @package Shulha\Framework\Session
 */
class Session
{
    /**
     * @var string $data Session variable that keeps general information of current session
     */
    private $data = 'session';

    /**
     * @var bool $started Is session started?
     */
    private $started = false;

    /**
     * Session constructor
     */
    public function __construct()
    {
        session_start();
        if (!isset($_SESSION[$this->data])) {
            $this->init();
        }

        $this->started = true;
    }

    /**
     * Check whether session is started or not
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Destroy session
     */
    public function destroy()
    {
        if (ini_get("session.use_cookies")) {
            setcookie(
                session_name(),
                session_id(),
                time() - 3600
            );
        }
        session_destroy();
    }

    /**
     * Return session name
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Set session variable "data"
     */
    public function init()
    {
        $_SESSION[$this->data] = array(
            'ip'       => $_SERVER['REMOTE_ADDR'],
            'name'     => session_name(),
            'created'  => $_SERVER['REQUEST_TIME'],
        );
    }

    /**
     * Check if given session variable $name exists or not
     *
     * @param $name
     * @return bool
     * @throws SessionException
     */
    public function exists($name)
    {
        if ($this->started === true) {
            return isset($_SESSION[$name]);
        } else {
            throw new SessionException("Session isn't started.");
        }
    }

    /**
     * Magic getter
     *
     * @param $name
     * @return null|string
     * @throws SessionException
     */
    public function __get($name)
    {
        if ($this->started === true) {
            return isset($_SESSION[$name])?$_SESSION[$name]:null;
        } else {
            throw new SessionException("Session isn't started.");
        }
    }

    /**
     * Magic setter
     *
     * @param $name
     * @param $value
     * @throws SessionException
     */
    public function __set($name, $value)
    {
        if ($this->started === true) {
            $_SESSION[$name] = $value;
        } else {
            throw new SessionException("Session isn't started.");
        }
    }

    /**
     * Remove specified session
     *
     * @param $name
     * @throws SessionException
     */
    public function remove($name)
    {
        if ($this->started !== true) {
            throw new SessionException("Session isn't started.");
        } else {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Add flash message to $_SESSION['flashMsgs'] array
     *
     * @param $name
     * @param $value
     * @throws SessionException
     */
    public function flash($name, $value)
    {
        if (!is_string($value)) {
            $parameterType = gettype($value);
            throw new SessionException(
                "Second parameter for Session::flash method must be 'string', '$parameterType' is given"
            );
        } else {
            $flashMsgs        = $this->exists('flashMsgs')?$this->__get('flashMsgs'):array();
            $flashMsgs[$name] = $value;
            $this->__set('flashMsgs', $flashMsgs);
        }
    }

    /**
     * Add error list from validation to $_SESSION['errorList'] array
     *
     * @param $value
     * @throws SessionException
     */
    public function flashErrorList($value)
    {
        if (!is_array($value)) {
            $parameterType = gettype($value);
            throw new SessionException(
                "Second parameter for Session::flashErrorList method must be 'array', '$parameterType' is given"
            );
        } else {
            $this->__set('errorList', $value);
        }
    }
}