<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;

use src\controller\LoginController;
use src\repository\CollectionRepository;
use src\repository\UserRepository;

class CollectionService {

    protected $collection_repository;
    protected $user_repository;

    function __construct(CollectionRepository $collection_repository, UserRepository $user_repository)
    {
        $this->collection_repository = $collection_repository;
        $this->user_repository = $user_repository;
    }

    /**
     * Return the collection for the user
     *
     * @param $user_id
     * @param $label
     * @return mixed
     */
    public function getCollection($user_id, $label)
    {
        return $this->collection_repository->getCollectionByLabel($user_id, $label);
    }

    /**
     * Return the inbox collection for the second user if the users are friends
     *
     * @param $user1_id
     * @param $user2
     * @return mixed
     * @throws \Exception
     */
    public function getForeignInboxCollection($user1_id, $user2)
    {
        // Get id of user2
        if ( preg_match($user2, LoginController::EMAIL_PATTERN) ) {
            $user2 = $this->user_repository->getIdByMail($user2);
        } else {
            $user2 = $this->user_repository->getIdByUser($user2);
        }

        if ( !$this->user_repository->areFriends($user1_id, $user2)) {
            throw new \Exception('error.not.friends');
        }

        return $this->getOwnInboxCollection($user2);
    }

    /**
     * Return the inbox collection of the given user
     *
     * @param $user_id
     * @return mixed
     */
    public function getOwnInboxCollection($user_id)
    {
        return $this->collection_repository->getInboxCollection($user_id);
    }
}