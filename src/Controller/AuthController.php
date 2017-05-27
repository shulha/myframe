<?php

namespace Shulha\Framework\Controller;

use Shulha\Framework\Controller\Exception\AuthRequiredException;
use Shulha\Framework\Request\Request;
use Shulha\Framework\Security\Security;
use Shulha\Framework\Security\UserContract;
use Shulha\Framework\Session\Session;

/**
 * Class AuthController
 * @package Shulha\Framework\Controller
 */
class AuthController extends Controller
{
    /**
     * Authorize user
     */
    public function login()
    {
        return view('system/login');
    }

    /**
     * Registration user
     */
    public function registration()
    {
        return view('system/registration');
    }

    /**
     * Logout user
     */
    public function logout(Session $session)
    {
        $session->destroy();

        return $this->redirect();
    }

    /**
     * Authorize user
     *
     * @param Request $request
     * @param UserContract $user
     * @param Security $security
     * @return \Shulha\Framework\Response\RedirectResponse
     * @throws AuthRequiredException
     */
    public function signin(Request $request, UserContract $user, Security $security){
        $users = $user->qb->table($user->table)->where('login', '=', $request->login)
            ->setFetchMode ( \PDO::FETCH_CLASS , get_class($user) , [$user->dbo] )->get();
        $user = array_shift($users);

        if($user && $user->checkCredentials($request)){
            $security->authorize($user);
            return $this->redirect();
        } else {
            throw new AuthRequiredException('User with these credentials not found, please check it once again');
        }
    }

    /**
     * User Registration
     *
     * @param Request $request
     * @param UserContract $user
     * @return \Shulha\Framework\Response\RedirectResponse
     */
    public function saveReg(Request $request, UserContract $user)
    {
        $user->insert(['login', 'password'], [$request->login, md5($request->password)]);

        return $this->redirect('login');
    }
}