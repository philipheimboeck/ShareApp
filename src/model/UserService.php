<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;


use src\repository\UserRepository;

class UserService {

    protected $user_repository;

    function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function checkLogin($email, $password) {
        $data = $this->user_repository->getPassword($email);
        if ( $data ) {
            return $password === $data;
        }

        return false;
    }

    public function getCollections($email) {
        return $this->user_repository->getCollections($email);
    }

    public function getFriends($email)
    {
        return $this->user_repository->getFriends($email);
    }

}