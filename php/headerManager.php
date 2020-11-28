<?php

namespace Managers;

/**
 * Class HeaderManager
 * @package Managers
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
Class HeaderManager{

    /**
     * gen_header : generate the HTML header using HEREDOC (https://www.php.net/manual/fr/language.types.string.php#language.types.string.syntax.heredoc)
     * @param $title string page title
     * @param $description string page description
     * @return string header
     */
    public static function gen_header($title, $description){
        return <<<EOD
<!DOCTYPE html>


<html lang="fr">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="$description">
    <meta name="keywords" content="toilet, analyzer, guard, stats">
    <meta name="author" content="Simon Gardier">
    
    <title>$title</title>
    
    <link rel="shortcut icon" href="flush.jpg"/>
    <link rel="stylesheet" href="bootstrap-4.5.2-dist/css/bootstrap.min.css" type="text/css" />
    <link rel="stylesheet" href="css/style.css" type="text/css"/>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="bootstrap-4.5.2-dist/js/bootstrap.min.js"></script>
</head>


<body>
    <nav class="navbar navbar-expand-md navbar-light bg-light border-bottom text-white" id="top_nav">
        <a class="navbar-brand text-white" href="index.php">Toilet Security Guard</a>
    </nav>
EOD;
    }
}
