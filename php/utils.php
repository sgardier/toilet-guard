<?php
namespace Utility;
Class Utils{
    public static function genAlerts($alerts){
        foreach ($alerts as $alert){
            if(!isset($alert['type']) or !isset($alert['message'])){
                return;
            }
            echo    '<div class="alert alert-'.htmlspecialchars($alert['type']).' alert-dismissible fade show custom-alert" role="alert">
                        <strong>Wow ! </strong>'.htmlspecialchars($alert['message']).'
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
}