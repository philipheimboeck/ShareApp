<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:07
 */

namespace src\repository;


use Symfony\Component\Config\Definition\Exception\Exception;

class UserRepository extends AbstractRepository
{

    /**
     * Returns the id to the corresponding username
     *
     * @param string $username
     * @return mixed
     */
    public function getIdByUser($username)
    {
        $sql = 'SELECT id FROM user WHERE username = ?';
        return $this->database->fetchColumn($sql, array($username));
    }

    /**
     * Returns the id to the corresponding mail
     *
     * @param string $email
     * @return mixed
     */
    public function getIdByMail($email) {
        $sql = 'SELECT id FROM user WHERE email = ?';
        return $this->database->fetchColumn($sql, array($email));
    }

    /**
     * Return the user number by his email
     *
     * @param string $email
     * @return array
     */
    public function isUserExistingByMail($email)
    {
        $sql = 'SELECT COUNT(*) FROM "user" WHERE "user".email = ?';
        return $this->database->fetchColumn($sql, array($email)) > 0;
    }

    public function isUserExistingById($user_id)
    {
        $sql = 'SELECT COUNT(*) FROM "user" WHERE "user".id = ?';
        return $this->database->fetchColumn($sql, array($user_id)) > 0;
    }

    /**
     * Return the password and the salt in an associative array
     *
     * @param string $id
     * @return array
     */
    public function getPassword($id)
    {
        $sql = 'SELECT "user".password, "user".salt FROM "user" WHERE "user".id = ?';
        return $this->database->fetchAssoc($sql, array($id));
    }

    /**
     * Return all collections of the user
     *
     * @param string $id
     * @return array
     */
    public function getCollections($id)
    {
        $sql = 'SELECT * FROM user_collection WHERE user_collection.user = ?';
        return $this->database->fetchAll($sql, array($id));
    }

    /**
     * Get all approved friends of the user
     *
     * @param string $id
     * @return array The friends emails
     */
    public function getFriends($id)
    {
        $sql = 'SELECT * FROM friendship WHERE friendship.accepted = 1 AND (friendship.user1 = :id OR friendship.user2 = :id)';
        $friends = array();

        foreach ($this->database->fetchAll($sql, array('id' => $id)) as $friendship) {
            if ($id === $friendship['user1']) {
                $friends[] = $friendship['user2'];
            } else {
                $friends[] = $friendship['user1'];
            }
        }
        return $friends;
    }

    /**
     * Checks if the two given users are already friends (or if there is an request)
     *
     * @param string $user1_id
     * @param string $user2_id
     * @param bool $accepted
     * @return bool
     */
    public function areFriends($user1_id, $user2_id, $accepted = true)
    {
        $sql = 'SELECT COUNT(*) FROM friendship WHERE ((friendship.user1 = :user1 AND friendship.user2 = :user2) OR  (friendship.user1 = :user2 AND friendship.user2 = :user1))';
        if ( $accepted ) {
            $sql .= ' AND friendship.accepted = 1';
        }
        return $this->database->fetchColumn($sql, array('user1' => $user1_id, 'user2' => $user2_id)) > 0;
    }

    /**
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $salt
     * @throws \Doctrine\DBAL\ConnectionException
     * @return bool|string
     */
    public function registerUser($email, $username, $password, $salt)
    {
        $this->database->beginTransaction();
        try {
            // Add user
            $user_result = $this->database->insert('user', array('email' => $email, 'username' => $username, 'password' => $password, 'salt' => $salt, 'active' => 1));
            if (empty($user_result)) {
                $this->database->rollback();
                return false;
            }
            $user_id = $this->database->lastInsertId();

            // Add inbox collection
            $inbox_result = $this->database->insert('collection', array('label' => $username . ' inbox', 'public' => 0));
            if (empty($inbox_result)) {
                $this->database->rollback();
                return false;
            }
            $inbox_id = $this->database->lastInsertId();

            $inbox_user_result = $this->database->insert('user_collection', array('user' => $user_id, 'collection' => $inbox_id, 'is_admin' => 1, 'is_default' => 1));
            if (empty($inbox_user_result)) {
                $this->database->rollback();
                return false;
            }

            $this->database->commit();
        } catch (Exception $ex) {
            $this->database->rollback();
            throw $ex;
        }

        return $user_id;
    }

    /**
     * @param string $id
     * @return array
     */
    public function getFriendRequests($id)
    {
        $sql = 'SELECT * FROM friendship WHERE friendship.user2 = ? AND friendship.accepted = 0';
        return $this->database->fetchAll($sql, array($id));
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getFriendRequestsNumber($id)
    {
        $sql = 'SELECT COUNT(*) FROM friendship WHERE friendship.user2 = ? AND friendship.user2 = 0';
        return $this->database->fetchColumn($sql, array($id));
    }

    /**
     * Returns the id, email and the username in an associative array
     *
     * @param $user_id
     * @return array
     */
    public function getUserData($user_id)
    {
        $sql = 'SELECT id, email, username FROM user WHERE id = ?';
        return $this->database->fetchAssoc($sql, array($user_id));
    }
}