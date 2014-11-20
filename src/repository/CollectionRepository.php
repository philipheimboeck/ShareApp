<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:07
 */

namespace src\repository;


class CollectionRepository extends AbstractRepository {


    public function getInboxCollection($id)
    {
        $sql = 'SELECT user_collection.collection FROM user_collection WHERE user_collection.user = ? AND user_collection.is_default LIMIT 1';
        return $this->database->fetchColumn($sql, array($id));
    }

    public function getCollectionByLabel($id, $label)
    {
        $sql = 'SELECT collection.id FROM collection INNER JOIN user_collection ON collection.id = user_collection.collection WHERE user_collection.user = ? AND collection.label LIKE ?';
        return $this->database->fetchColumn($sql, array($id, '%' . $label . '%'));
    }
}