<?php
namespace Repositories;

require_once 'inc/config.inc.php';
require_once 'inc/db_link.inc.php';

use DB\DBLink;
use PDO;

/**
 * Class LogsRepository
 * @package Repositories
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
class LogsRepository {
    const TABLE_NAME = HISTORY;

    /**
     * addLogInDb($firstname) : Add a toilet log in the logs
     * @param $firstname firstname of the user adding a new toilet log
     */
    public static function addLogInDb($firstname) {
        $bdd = DBLink::connect2db(MYDB);

        $sql = 'INSERT INTO '.SELF::TABLE_NAME.'(firstname, date, spotted) VALUES(\''.$firstname.'\', DATE_ADD(NOW(), INTERVAL 1 hour), false) ';

        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
        
        DBLink::disconnect($bdd);
    }

    /**
     * getAllLogs() : Get all the toilet logs
     * @return array of all the toilet logs
     */
    public static function getAllLogs() {
        $bdd = DBLink::connect2db(MYDB);

        $sql = 'SELECT * FROM '.SELF::TABLE_NAME;
        
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        DBLink::disconnect($bdd);

        return $results;
    }

    /**
     * setSpotted($id) : Set the log having the id $id as spotted
     * @param $id of the log to find
     */
    public static function setSpotted($id){
        $bdd = DBLink::connect2db(MYDB);

        $sql = 'UPDATE '.SELF::TABLE_NAME.' SET spotted = 1 WHERE id='.$id;
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();
        
        DBLink::disconnect($bdd);
    }

    /**
     * getStats() : Get all the stats
     * @return array of the toilet stats
     */
    public static function getStats() {
        $bdd = DBLink::connect2db(MYDB);

        $sql = 'SELECT firstname, COUNT(id) FROM '.SELF::TABLE_NAME.' WHERE spotted = 1 GROUP BY firstname';
        
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        DBLink::disconnect($bdd);

        return $results;
    }
}
