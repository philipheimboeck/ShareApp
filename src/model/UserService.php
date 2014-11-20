<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;


use src\controller\LoginController;
use src\repository\UserRepository;

class UserService
{

    protected $user_repository;

    function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function checkLogin($user, $password)
    {
        $user_id = null;
        if (preg_match(LoginController::EMAIL_PATTERN, $user)) {
            $user_id = $this->user_repository->getIdByMail($user);
        } else {
            $user_id = $this->user_repository->getIdByUser($user);
        }
        $safed_password = $this->user_repository->getPassword($user_id);

        if ($safed_password) {
            if ($this->hashPassword($password, $safed_password['salt']) === $safed_password['password']) {
                return $this->user_repository->getUserData($user_id);
            }
        }

        return false;
    }

    public function getCollections($id)
    {
        return $this->user_repository->getCollections($id);
    }

    public function getFriends($id)
    {
        return $this->user_repository->getFriends($id);
    }

    public function register($email, $username, $password)
    {
        // User already exists?
        if (!($this->user_repository->isUserExistingByMail($email))) {
            // Generate salt
            $salt = md5(uniqid('SECRET_SHARE_SALT'));

            // Hash password with salt
            $password = $this->hashPassword($password, $salt);

            $user_id = $this->user_repository->registerUser($email, $username, $password, $salt);
            if ($user_id) {
                $this->user_repository->getUserData($user_id);
            }
        }
        return false;
    }

    protected function hashPassword($password, $salt)
    {
        return md5($password . $salt);
    }

}