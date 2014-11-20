<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;

use src\controller\LoginController;
use src\repository\FriendshipRepository;
use src\repository\UserRepository;

class FriendshipService
{
    protected $user_repository;
    protected $friendship_repository;

    function __construct(FriendshipRepository $friendship_repository, UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
        $this->friendship_repository = $friendship_repository;
    }

    /**
     * Get the outstanding requests
     *
     * @param string $id
     * @return array
     */
    public function getRequests($id)
    {
        return $this->user_repository->getFriendRequests($id);
    }

    /**
     * Get the number of outstanding requests
     *
     * @param string $id
     * @return mixed
     */
    public function getRequestsNumber($id) {
        return $this->user_repository->getFriendRequestsNumber($id);
    }

    /**
     * Send a friend request to a friend
     *
     * @param string $id
     * @param string $friend the email or username of the friend
     * @throws \Exception
     * @return bool
     */
    public function sendRequest($id, $friend)
    {
        $friend_id = null;
        if ( !preg_match(LoginController::EMAIL_PATTERN, $friend)) {
            $friend_id = $this->user_repository->getIdByUser($friend);
        } else {
            $friend_id = $this->user_repository->getIdByMail($friend);
        }

        if ( $friend_id === null ) {
            throw new \Exception('user.not.existing');
        }

        if ( ($this->user_repository->areFriends($id, $friend_id, false))) {
            throw new \Exception("request.existing");
        }

        return $this->friendship_repository->createFriendRequest($id, $friend_id);
    }

    /**
     * Either accept or delete the request
     *
     * @param $id
     * @param $request_id
     * @param $accept
     * @return bool
     * @throws \Exception
     *
     */
    public function answerRequest($id, $request_id, $accept)
    {
        // Is the user allowed to accept the friendship?
        if ( $this->friendship_repository->getUsers($request_id)['user2'] !== $id ) {
            throw new \Exception("request.acceptance.not.allowed");
        }

        if ( $accept) {
            // Accept the request
            return $this->friendship_repository->acceptFriendship($request_id);
        } else {
            // Delete the request
            return $this->friendship_repository->deleteFriendship($request_id);
        }
    }

}