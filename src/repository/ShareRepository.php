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

    public function getUserShares($username)
    {
        $sql = 'SELECT * FROM share WHERE share."user" = ?';
        return $this->database->fetchAll($sql, array($username));
    }

    public function createShare($email, $content, $collection)
    {
        return $this->database->insert('share',
            array('user' => $email, 'content' => $content, 'collection' => $collection));
    }
}