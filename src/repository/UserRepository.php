<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:07
 */

namespace src\repository;


class UserRepository extends AbstractRepository {

    /**
     * Return the user by his email
     *
     * @param $email
     * @return array
     */
    public function getUser($email) {
        $sql  = 'SELECT * FROM "user" WHERE "user".email = ?';
        return $this->database->fetchAssoc($sql, array($email));
    }

    /**
     * Return the password of the user
     *
     * @param $email
     * @return string
     */
    public function getPassword($email) {
        $sql  = 'SELECT "user".password FROM "user" WHERE "user".email = ?';
        return $this->database->fetchColumn($sql, array($email));
    }

    /**
     * Return all collections of the user
     *
     * @param $email
     * @return array
     */
    public function getCollections($email)
    {
        $sql = 'SELECT * FROM user_collection WHERE user_collection.user = ?';
        return $this->database->fetchAll($sql, array($email));
    }

    /**
     * Get all approved friends of the user
     *
     * @param $email
     * @return array The friends emails
     */
    public function getFriends($email)
    {
        $sql = 'SELECT * FROM friendship WHERE friendship.user2_approved = 1 AND (friendship.user1 = :email OR friendship.user2 = :email)';
        $friends = array();

        foreach ( $this->database->fetchAll($sql, array('email' => $email)) as $friendship ) {
            if ( $email === $friendship['user1'] ) {
                $friends[] = $friendship['user2'];
            } else {
                $friends[] = $friendship['user1'];
            }
        }
        return $friends;
    }

    /**
     * Checks if the two given users are already friends
     *
     * @param $user1
     * @param $user2
     * @return bool
     */
    public function areFriends($user1, $user2)
    {
        $sql = 'SELECT COUNT(*) FROM friendship WHERE friendship.user2_approved = 1 AND (friendship.user1 = :user1 AND friendship.user2 = :user2) OR  (friendship.user1 = :user2 AND friendship.user2 = :user1)';
        return $this->database->fetchColumn($sql, array('user1' => $user1, 'user2' => $user2)) > 0;
    }
}