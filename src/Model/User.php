<?php

namespace Shulha\Framework\Model;

use Shulha\Framework\Request\Request;
use Shulha\Framework\Security\UserContract;

/**
 * Class User
 *
 * @package Shulha\Framework\Model
 */
class User extends Model implements UserContract
{
    /**
     * @var string
     */
    public $table = 'users';

    /**
     * @inheritdoc
     */
    public function isGuest(): bool
    {
        return !(bool)$this->id;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        $roles = explode(',', $this->roles);

        return (array)$roles;
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials(Request $request): bool
    {
        return (($this->login === $request->login) && (md5($request->password) === $this->password));
    }
}