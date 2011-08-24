<?php
/**
 * Description of UserUI
 * Functions dedicated to providing user information and means of editing that information in a webpage format.
 * The script which to post data to about users.
 * 
 * @author cameron
 * @name UserUI
 * @copyright Australian Network for Art and Technology 2011
 */

require_once ('user.php');
require_once('webTools.php');

class userUI {
    private $user;
    
    public function __construct() {
        $this->user = new user();
        webTools::phpErrorsOn();
    }
    
    public function checkCookie() {
        $user = $this->user->checkCookie();
        if($user) {
            echo $user;
        }
        else {
            echo "<p>not logged in</p>";
        }
    }
    private function createCookie() {
        $res = $this->user->adjustCookie($_POST['email'], $_POST['pass'], $_SERVER['REMOTE_ADDR']);
        if($res != 200) echo "<p>".$res."</p>";
        else header("Location: ".webTools::currentURL());
    }
    

    private function login() {
        if(($_POST['email']) && ($_POST['pass'])) {
            if($this->user->authenticate($_POST['email'], $_POST['pass']) == 200) {
                //set cookie & refresh page
                $this->createCookie();
                
                //echo "Success";
            }
            else {
                echo "<p>Authentication failed</p>";
            }
        }
        else {
            echo "<p>Login Error :: email or password missing.</p>";
        }
    }
    /*outputs a login form on request */
    public function loginForm() {
        if(!($this->user->checkCookie())) {
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
             "<h2>Login</h2>".PHP_EOL.
             "<p><label>Login/email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
             "<p><label>Password: <input type=\"password\" name=\"pass\"></label></p>".PHP_EOL.
             "<p><label><input type=\"hidden\" name=\"CMD\" value=\"login\"></label></p>".PHP_EOL.
             "<p><input type=\"submit\" value=\"login\"></p>".PHP_EOL.
             "</form>".PHP_EOL;
        }
    }
    
    private function register() {
        if((isset($_POST['email'])) && (isset($_POST['fname'])) && (isset($_POST['lname']))) {
            if ($this->user->register($_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['pass']) == 409) {
                echo "<p>Error: email already registered.</p>";
            }
        }
        else {
            echo "<p>Unable to register, email, first name and last name required!</p>";
        }
    }
    public static function registerForm() {
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
             "<h3>Register</h3>".PHP_EOL.
             "<p><label>Login/email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
             "<p><label>Firstname: <input type=\"text\" name=\"fname\"></label></p>".PHP_EOL.
             "<p><label>Surname: <input type=\"text\" name=\"lname\"></label></p>".PHP_EOL.
             "<p><label>Password: <input type=\"password\" name=\"pass\"></label></p>".PHP_EOL.
             "<p><label><input type=\"hidden\" name=\"CMD\" value=\"register\"></label></p>".PHP_EOL.
             "<p><input type=\"submit\" value=\"Register\"></p>".PHP_EOL.
             "</form>".PHP_EOL;
    }
    
    static function resetPass() {
        if($_POST['email']) {
            echo $this->user->resetPassword($_POST['email']);
        }
    }
    
    public function postHandle() {
        if(isset($_POST['CMD'])) {
            //switch case
            switch($_POST['CMD']) {
                case "login": {
                    $this->login();
                    break;
                }
                case "resetPass": {
                    $this->resetPass();
                    break;
                }
                case "register" : {
                    $this->register();
                    break;
                }
            }
        }
    }
    public function updateCredentials() {
        //check user is logged in
        if($this->checkCookie())
            if($this->user->authenticate($_POST['email'], $_POST['pass'])) //check authentication again
                $this->user->updateCredentials($oldpass, $passwd, $email, $newemail); //update
    }
    public static function updateCredentialsForm() {
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
              "<p><label>Current Email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
              "<p><label>Current Password: <input type=\"text\" name=\"pass\"></label></p>".PHP_EOL.
              "<p><label>*leave 'new' fields blank to not change.</label></p>".PHP_EOL.
              "<p><label>New Email: <input type=\"text\" name=\"newemail\"></label></p>".PHP_EOL.
              "<p><label>New password: <input type=\"text\" name=\"newpass\"></label></p>".PHP_EOL.
              "<p><label><input type=\"hidden\" name=\"CMD\" value=\"updateCredentials\"></label></p>".PHP_EOL.
              "<p><label><input type=\"submit\" value=\"update\"</label></p>".PHP_EOL.         
              "</form>".PHP_EOL;
    }    
}

/* START 
/* enforce posting method */

if($_SERVER['REQUEST_METHOD'] == "POST") {
    webTools::cleanArray($_POST);
    $ui = new userUI;
    $ui->postHandle();
    //$ui->checkCookie();
}
//
/*END*/
?>