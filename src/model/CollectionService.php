<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 18:41
 */

namespace src\model;

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
     * @param $email
     * @return mixed
     */
    public function getCollection($email, $label)
    {
        return $this->collection_repository->getCollectionByLabel($email, $label);
    }

    /**
     * Return the inbox collection for the second user if the users are friends
     *
     * @param $user1
     * @param $user2
     * @return mixed
     * @throws \Exception
     */
    public function getForeignInboxCollection($user1, $user2)
    {
        if ( !$this->user_repository->areFriends($user1, $user2)) {
            throw new \Exception('error.not.friends');
        }

        return $this->getOwnInboxCollection($user2);
    }

    /**
     * Return the inbox collection of the given user
     *
     * @param $email
     * @return mixed
     */
    public function getOwnInboxCollection($email)
    {
        return $this->collection_repository->getInboxCollection($email);
    }
}