<?php
/**
 * User: Philip Heimböck
 * Date: 06.11.14
 * Time: 17:08
 */

namespace src\repository;


use Doctrine\DBAL\Connection;

abstract class AbstractFacade {

    /** @var  Connection */
    protected $database;

    function __construct($database)
    {
        $this->database = $database;
    }
}