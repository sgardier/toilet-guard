<?php
namespace Managers;

require_once 'php/repositories/user-repository.php';
require_once 'php/utils.php';

use Repositories\HistoryRepository;
use Utility\Utils;

/**
 * Class IndexManager
 * @package Managers
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
Class IndexManager{
    /**
     * gen_main()
     * @return string
     */
    public static function gen_main(){
        $alerts = isset($_GET['alerts']) ? Utils::genAlerts($_GET['alerts']) : '';
        $alerts_to_main = [
            'alerts' => [
                ['type' => 'success', 'message'=>'Identifié(e) !']
            ]
        ];
        return '<main class="container">
                    '. $alerts .'
                    <form action="main.php?'.http_build_query($alerts_to_main).'" method="POST" class="w-50 vh-100 d-flex justify-content-center align-items-center flex-column m-auto">
                        <input required type="text" class="form-control m-2" id="firstname" name="firstname" placeholder="Prénom">
                        <input required type="password" class="form-control m-2" id="password" name="password" placeholder="Mot de passe">
                        <input type="submit" value="Se connecter" class="btn btn-primary m-2">
                    </form>
                </main>';
    }
}