<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;


use src\repository\UserFacade;

class UserService {

    protected $user;

    function __construct(UserFacade $user)
    {
        $this->user = $user;
    }

    public function checkLogin($username, $password) {
        $data = $this->user->getPassword($username);
        if ( $data ) {
            return $password === $data;
        }

        return false;
    }

} 