<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:07
 */

namespace src\repository;


class ShareRepository extends AbstractRepository
{

    public function getPublicShares()
    {
        $sql = 'SELECT * FROM share INNER JOIN collection ON collection.id = share.collection WHERE collection.public = TRUE ORDER BY share.created_at DESC';
        return $this->database->fetchAssoc($sql);
    }

    public function getUserShares($id)
    {
        $sql = 'SELECT share.* FROM share INNER JOIN user_collection ON user_collection.id = share.collection WHERE user_collection.user = ?';
        return $this->database->fetchAll($sql, array($id));
    }

    public function createShare($id, $content, $collection)
    {
        return $this->database->insert('share',
            array('user' => $id, 'content' => $content, 'collection' => $collection));
    }
}