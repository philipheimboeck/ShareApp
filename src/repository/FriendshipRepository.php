<?php
/**
 * User: Philip Heimböck
 * Date: 20.11.14
 * Time: 16:50
 */

namespace src\repository;


class FriendshipRepository extends AbstractRepository {
    /**
     * Accept a friendship request‚
     *
     * @param $request_id
     * @return bool
     */
    public function acceptFriendship($request_id)
    {
        return !empty($this->database->update('friendship', array('accepted' => 1), array('id' => $request_id)));
    }

    /**
     * Delete a friendship or a request
     *
     * @param $request_id
     * @return bool
     */
    public function deleteFriendship($request_id)
    {
        return !empty($this->database->delete('friendship', array('id' => $request_id)));
    }

    /**
     * @param string $user_id
     * @param string $friend_id
     * @return boolean
     */
    public function createFriendRequest($user_id, $friend_id)
    {
        return !empty($this->database->insert('friendship', array('user1' => $user_id, 'user2' => $friend_id, 'accepted' => 0)));
    }

    public function getUsers($friendship_id)
    {
        $sql = 'SELECT user1, user2 FROM friendship WHERE id = ?';
        return $this->database->fetchAssoc($sql, array($friendship_id));
    }
} 