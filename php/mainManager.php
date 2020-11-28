<?php
namespace Managers;

require_once 'php/repositories/logs-repository.php';
require_once 'php/identificationManager.php';
require_once 'php/utils.php';

use Repositories\LogsRepository;
use Utility\Utils;

/**
 * Class MainManager
 * @package Managers
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
Class MainManager{
    /**
     * handlingEvents($firstname) manage the clicks events (spotting a log or flushing)
     * @param $identity
     */
    public static function handlingEvents($identity){
        if(!$identity['identified']){
            return;
        }
        if(!empty($_POST) and isset($_POST['token']) and IdentificationManager::isAuthenticatedFormRequest($_POST['token'])) {
            if (isset($_POST['flushing']) and $_POST['flushing'] == 1){
                self::flushing($identity['firstname']);
            }

            else if (isset($_POST['spotted'])){
                self::spotting($_POST['spotted']);
            }
        }
    }

    /**
     * flushing($firstname) call the LogsRepository::addLogInDb($firstname) method
     * @param $firstname for who we need to add a new log in the DB
     */
    public static function flushing($firstname){
        LogsRepository::addLogInDb($firstname);
    }

    /**
     * spotting($spotted) call the LogsRepository::setSpotted($spotted) method
     * @param $spotted represent the id of the log spotted
     */
    public static function spotting($spotted){
        LogsRepository::setSpotted($spotted);
    }
    /**
     * gen_main() concat all the html elements needed to create the <main> of main.php
     * @return string the entire <main> in string format
     */
    public static function gen_main(){
        $alerts = isset($_GET['alerts']) ? Utils::genAlerts($_GET['alerts']) : '';
        if(session_status() != PHP_SESSION_NONE){
            $token = $_SESSION['token'];
        }
       return '<main class="container">'. $alerts . self::gen_toilet_form($token) . self::gen_logs($token) . self::gen_stats().'</main>';
    }

    /**
     * gen_toilet_form() genenerate the form containing the flush image (wich is clickable and trigger the event flushing)
     * @return string form to add a toilet log
     */
    public static function gen_toilet_form($token){
        return  '<form action="'.$_SERVER['PHP_SELF'].'" method="POST" class="d-flex justify-content-center align-items-center">
                    <input type="number" value="1" name="flushing" id="flushing" hidden>
                    <input type="text" value="'.$token.'" name="token" id="token" hidden>
                    <input type="submit" value="" name="poop" id="poop" class="mt-5 mb-5">
                </form>';
    }

    /**
     * gen_logs() generete the <table> for the 10 last logs of flushing
     * @return string table with the toilet logs
     */
    public static function gen_logs($token){
        $logs = LogsRepository::getAllLogs();
        $rows = '';
        for ($i = sizeof($logs) - 1; $i >= max(sizeof($logs)-10,0); $i--) {
            $spotted = $logs[$i]['spotted'];
            $color = $spotted ? 'bg-danger' : '';
            $disabled = $spotted ? 'disabled' : '';
            $rows .='<tr class="' . $color .'">
                        <th scope="row">' . $logs[$i]['id'] . '</th>
                        <td>' . $logs[$i]['firstname'] . '</td>
                        <td>' . date("l", strtotime($logs[$i]['date'])) . '</td>
                        <td>' . date("H:i", strtotime($logs[$i]['date'])) . '</td>
                        <td>
                            <form action="" method="POST">
                                <input type="text" value="' . $logs[$i]['id'] . '" id="spotted" name="spotted" hidden>
                                <input type="text" value="'.$token.'" name="token" id="token" hidden>
                                <input type="submit" value="Signaler" class="btn btn-warning" ' . $disabled . '>
                            </form>
                        </td>
                    </tr>' . "\n";
        }
        return '<h2 class="text-center m-2">Historique</h2>
                <div class="overflow-custom">
                    <table class="table bg-light" id="logs">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Prénom</th>
                                <th scope="col">Jour</th>
                                <th scope="col">Heure</th>
                                <th scope="col">Signalement</th>
                            </tr>
                        </thead>
                        <tbody>
                            '.$rows.'
                        </tbody>
                    </table>
                </div>';
    }

    /**
     * generate the stats for each user (Quantity of spotted, TODO)
     * @return string table with the users stats
     */
    public static function gen_stats(){
        $stats = LogsRepository::getStats();
        $rows = '';
        for ($i = 0; $i <= sizeof($stats) - 1; $i++){
            $rows.='<tr>
                        <th scope="row">'.$stats[$i]['firstname'].'</th>
                        <td>'.$stats[$i]['COUNT(id)'].'</td>
                    </tr>';
        }

        return '<h2 class="text-center m-2">Statistiques</h2>
                <div class="overflow-custom">
                    <table class="table bg-light" id="stats">
                        <thead>
                            <tr>
                                <th scope="col">Prénom</th>
                                <th scope="col">Spotted</th>
                            </tr>
                        </thead>
                        <tbody>
                            '.$rows.'
                        </tbody>
                    </table>
                </div>';
    }
}