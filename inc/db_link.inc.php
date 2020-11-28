<?php
namespace DB;
require_once 'config.inc.php';

use PDO;
use PDOException;

/**
 * Classe DBLink : DB Connection manager
 * @author Simon GARDIER
 * @maj     : 2020.11.05
 * @version 3.0
 */
class DBLink
{
    /**
     * Connect to the DB (from ../inc/config.inc.php)
     * @return PDO|false PDO Object or false if connection fail
     * @var string $base DB name
     */
    public static function connect2db($base)
    {
        try {
            $link = new PDO('mysql:host=' . MYHOST . ';dbname=' . $base . ';charset=UTF8', MYUSER, MYPASS);
            $link->exec("set names utf8");
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {;
            $link = false;
        }
        return $link;
    }

    /**
     * DB disconnection
     * @var PDO $link PDO Object
     */
    public static function disconnect(&$link)
    {
        $link = null;
    }
}
