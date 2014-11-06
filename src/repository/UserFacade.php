<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:07
 */

namespace src\repository;


class UserFacade extends AbstractFacade {

    public function getUser($email) {
        $sql  = 'SELECT * FROM "user" WHERE "user".email = ?';
        return $this->database->fetchAll($sql, array($email));
    }

    public function getPassword($email) {
        $sql  = 'SELECT "user".password FROM "user" WHERE "user".email = ?';
        return $this->database->fetchColumn($sql, array($email));
    }
} 