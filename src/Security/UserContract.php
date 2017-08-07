<?php

namespace Shulha\Framework\Security;

use Shulha\Framework\Request\Request;

/**
 * Interface UserContract
 * @package Shulha\Framework\Security
 */
interface UserContract
{
    /**
     * Check if user is a guest
     *
     * @return bool
     */
    public function isGuest(): bool;

    /**
     * Get user roles
     *
     * @return array
     */
    public function getRoles(): array;

    /**
     * Try to check if can be authorized with provided request data
     *
     * @param Request $request
     * @return bool
     */
    public function checkCredentials(Request $request): bool;
}