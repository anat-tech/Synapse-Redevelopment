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

include_once('user.php');
include_once('webTools.php');

class userUI {
    private $user;
    
    public function __construct() {
        $this->user = new user();
    }

    private function login() {
        if(($_POST['email']) && ($_POST['pass'])) {
            if($this->user->authenticate($_POST['email'], $_POST['pass']) == 200) {
                //set cookie
                //output stuff on success
                echo "Success";
            }
            else {
                echo "Authentication failed";
            }
        }
        else {
            echo "Login Error :: email or password missing. Web developer -> check form data!";
        }
    }
    /*outputs a login form on request */
    public static function loginForm() {
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
             "<p><label>Login/email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
             "<p><label>Password: <input type=\"text\" name=\"pass\"></label></p>".PHP_EOL.
             "<p><label><input type=\"hidden\" name=\"CMD\" value=\"login\"></label></p>".PHP_EOL.
             "<p><input type=\"submit\" value=\"login\"></p>".PHP_EOL.
             "</form>".PHP_EOL;
    }
    
    private function resetPass() {
        if($_POST['email']) {
            $this->user->resetPassword($_POST['email']);
        }
    }
    
    public function postHandle() {
        if(isset($_POST['CMD'])) {
            //switch case
            switch($_POST['CMD']) {
                case "login": {
                    $this->login();
                }
                case "resetPass": {
                    $this->resetPass();
                }
            }
        }
    }
    public function updateCredentials() {
        //check user is logged in
        //$this->user->updateCredentials($oldpass, $passwd, $email, $newemail)
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
}
/*END*/
?>