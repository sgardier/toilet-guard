<?php
namespace Repositories;

require_once 'inc/config.inc.php';
require_once 'inc/db_link.inc.php';

use DB\DBLink;
use PDO;

/**
 * Class UserRepository
 * @package Repositories
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
class UserRepository {
    const TABLE_NAME = USERS;

    /**
     * userExistInDb($firstname, $password) check if an user having <$firstname, password> AS <firstname => VARCHAR(50), password => VARCHAR(50)> exist
     * @param $firstname first name of the user
     * @param $password password of the user
     * @return bool is present or not in the DB
     */
    public static function userExistInDb($firstname, $password){
        $bdd = DBLink::connect2db(MYDB);

        $sql = 'SELECT firstname, password, COUNT(id) as qtt FROM '.SELF::TABLE_NAME.' WHERE firstname =\''.$firstname.'\' and password = \''.$password.'\' GROUP BY firstname';
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        
        DBLink::disconnect($bdd);
        $return = [
            'identified' => false,
            'firstname' => '',
            'password' => ''
        ];
        if($results != null and $results['qtt'] == 1){
            $return['identified'] = true;
            $return['firstname'] = $results['firstname'];
            $return['password'] = $results['password'];
        }
        return  $return;
    }
}
