<?php
namespace Managers;

require_once 'php/repositories/user-repository.php';
require_once 'php/encryptingManager.php';

use Repositories\UserRepository;

/**
 * Class IdentificationManager
 * @package Managers
 * @maj     : 2020.11.05
 * @author  : Simon Gardier
 */
Class IdentificationManager{
    /**
     * isIdentified() check if the client have a  know identity
     * @return array[bool, string] if he is identified, his first name (= '' if not identified)
     */
    public static function isIdentified(){
        $firstname = null;
        $isIdentified = false;
        if(self::isIdentifiedInCookies() or self::isIdentifiedInPost()){
            $firstname = self::isIdentifiedInCookies() ? $_COOKIE['firstname'] : $_POST['firstname'];
            $isIdentified = true;
            self::beginAuthenticatedSession();
            self::redirection(true);
        }
        else{
            self::redirection(false);
        }
        return ['identified'=>$isIdentified, 'firstname'=>$firstname];
    }

    private static function redirection($identified){
        $url = strtok($_SERVER["REQUEST_URI"], '?');
        if($identified){
            if(!strpos($url, 'main.php')){
                $isIdentified = true;
                header("location: main.php");
                exit;
            }
        }
        else{
            if(!strpos($url, 'index.php')){
                header("location: index.php?alert=Vous n'êtes pas reconnus");
                exit;
            }
        }
    }

    private static function beginAuthenticatedSession(){
        if(session_status() == PHP_SESSION_NONE){
            session_start();
        }
        if (session_status() != PHP_SESSION_NONE and !isset($_SESSION['token'])){
            $_SESSION['token'] = Cryptor::encrypt(bin2hex(openssl_random_pseudo_bytes(7)));
        }
    }

    public static function isAuthenticatedFormRequest($token){
        $isAuthenticated = false;
        if (session_status() != PHP_SESSION_NONE and isset($_SESSION['token']) and $_SESSION['token'] == $token){
            $isAuthenticated = true;
        }
        else{
            //kill the cookie of the frauder, bad
            setcookie('firstname');
            setcookie('password');
            $_SESSION['token'] = null;
            $alerts = [
                'alerts' => [
                    ['type' => 'warning', 'message'=>'Jeton erroné'],
                    ['type' => 'danger', 'message'=>'Jeton non-erroné']
                ]
            ];
            header( 'Location: index.php?'.http_build_query($alerts));
        }
        return $isAuthenticated;
    }
    /**
     * isIdentifiedWithCookies() check if a cookies already exist with an identity. Check the validity and return it
     * @return bool if there is an identity cookies and if it's valid
     */
    private static function isIdentifiedInCookies(){
        if(isset($_COOKIE['firstname']) and isset($_COOKIE['password'])){
            $firstname = htmlspecialchars($_COOKIE['firstname']);
            $password = htmlspecialchars($_COOKIE['password']);
            if(UserRepository::userExistInDb($firstname, $password)){
                return true;
            }
        }
    }

    /**
     * isIdentifiedInPost() check in $_POST if an identification request is stored. If yes and if identification is valid,
     * it create a new cookies ! Wow
     * @return bool is identified with a new cookies or not
     */
    private static function isIdentifiedInPost(){
        if(!empty($_POST) and isset($_POST['firstname']) and isset($_POST['password'])) {
             return self::createIdentification();
        }
    }
    /**
     * createIdentification() create a new cookies if user exist in DB
     * @return bool success or not of the operation
     */
    public static function createIdentification(){
        $firstname = htmlspecialchars($_POST['firstname']);
        $password = htmlspecialchars($_POST['password']);
        $identity = UserRepository::userExistInDb($firstname, $password);
        if($identity['identified']){
            setcookie('firstname', $identity['firstname'], time() + 365*24*3600, null, null, false, true);
            setcookie('password', $identity['password'], time() + 365*24*3600, null, null, false, true);
            return true;
        }
        return false;
    }
}