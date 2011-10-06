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
        //webTools::phpErrorsOn();
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
             "<p><input class=\"button\" type=\"submit\" value=\"login\"></p>".PHP_EOL.
             "</form>".PHP_EOL;
        }
    }
    
    private function logout() {
        //remove cookie
        $email = $this->user->checkCookie();
        $this->user->removeCookie($email);
        //header("refresh:3;url=".webTools::currentURL());
    }
    public function logoutForm() {
        if(($this->user->checkCookie())) {
            echo "<form action=\"".webTools::currentURL()."\" method=\"post\">".PHP_EOL.
                 "<label><input class=\"button\" type=\"submit\" name=\"CMD\" value=\"logout\"></label>".PHP_EOL.   
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
    public function registerForm() {
        if(($this->user->checkCookie())) return;
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
             "<h2>Register</h2>".PHP_EOL.
             "<p><label>Login/email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
             "<p><label>Firstname: <input type=\"text\" name=\"fname\"></label></p>".PHP_EOL.
             "<p><label>Surname: <input type=\"text\" name=\"lname\"></label></p>".PHP_EOL.
             "<p><label>Password: <input type=\"password\" name=\"pass\"></label></p>".PHP_EOL.
             "<p><label><input type=\"hidden\" name=\"CMD\" value=\"register\"></label></p>".PHP_EOL.
             "<p><input class=\"button\" type=\"submit\" value=\"Register\"></p>".PHP_EOL.
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
                case "logout": {
                    $this->logout();
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
                case "updateCredentials" : {
                    $this->updateCredentials();
                    break;
                }
                case "updateProfile" : {
                    $this->updateProfile();
                    break;
                }
            }
        }
    }
    private function updateCredentials() {
        //check user is logged in
        if($this->user->checkCookie() != false) {
            if($this->user->authenticate($_POST['email'], $_POST['pass'])) {//check authentication again 
                $ret = $this->user->updateCredentials($_POST['pass'], $_POST['newpass'], $_POST['email'], $_POST['newemail']); //update
                echo "<p>".$ret."</p>";
            }
        }
        //reload page after updating details
        header("Location: ".webTools::currentURL());
    }
    public function updateCredentialsForm() {
        if(($this->user->checkCookie())) {
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
              "<h2>Update Credentials</h2>".PHP_EOL.
              "<p><label>* Current Email: <input type=\"text\" name=\"email\"></label></p>".PHP_EOL.
              "<p><label>* Current Password: <input type=\"password\" name=\"pass\"></label></p>".PHP_EOL.
              "<p><label>* fields required.</label></p>".PHP_EOL.
              "<p><label><b>leave new fields blank to not change.</b></label></p>".PHP_EOL.
              "<p><label>New Email: <input type=\"text\" name=\"newemail\"></label></p>".PHP_EOL.
              "<p><label>New password: <input type=\"text\" name=\"newpass\"> :: WARNING Input is visible</label></p>".PHP_EOL.
              "<p><label><input type=\"hidden\" name=\"CMD\" value=\"updateCredentials\"></label></p>".PHP_EOL.              
              "<p><label><input class=\"button\" type=\"submit\" value=\"update\"></label></p>".PHP_EOL.         
              "</form>".PHP_EOL;
        }
    }
    
    private function updateProfile() {
        foreach($_POST as $key => $value) {
            $key = trim($key);
            //inaccessible fields
            if(($key != 'email') || ($key != "salt" ) || ($key != 'passwd') || (substr($key,0,5) != 'cookie')) {
                $GLOBALS['updateArry'][$key] = $value;
            }
            else {
                //return false;
                echo "fail";
                return false;
            }
        }
        $email = $this->user->checkCookie();
        if($email)
            $out = ($this->user->updateProfile($GLOBALS['updateArry'], $email));
            if($out == 200) echo "<p>update successful!</p>";
            else echo $out;
    }
    public function updateProfileForm(){
        $email = $this->user->checkCookie();
        if(!($email)) return false; //stop if not logged in
        
        $details = $this->user->getProfile($email);
        // profile fields: "firstname,lastname,email,email2,peopleStatement,url,image,image_caption,region,gallery_image,people_status"
        echo "<form action=\"".webtools::currentURL()."\" method=\"post\">".PHP_EOL.
             "<h2>Profile Details </h2>".PHP_EOL.
             "<p><label>Firstname: <input type=\"text\" size=\"32\" name=\"firstname\" value=\"".$details['firstname']."\"></label></p>".PHP_EOL.
             "<p><label>Surname: <input type=\"text\" size=\"32\" name=\"lastname\" value=\"".$details['lastname']."\"></label></p>".PHP_EOL.
             "<p><label>Alternative email: <input type=\"text\" size=\"42\" name=\"email2\" value=\"".$details['email2']."\"> </label></p>".PHP_EOL.
             "<p><label>Webpage: <input type=\"text\" name=\"url\" size=\"64\" value=\"".$details['url']."\"</label></p>".PHP_EOL.
             "<p><label>Image: <input type=\"file\" name=\"image\">".PHP_EOL.
             "<br><img src=\"data:image/gif;base64,".base64_decode($details['image'])."\" alt=\"\"></label></p>".PHP_EOL.
             "<p><label>Image caption: <input type=\"text\" name=\"image_caption\" size=\"64\" value=\"".$details['image_caption']."\"></label></p>".PHP_EOL.
             "<p><label>region:</label></p>".PHP_EOL.
             "<p><label></label></p>".PHP_EOL.
             "<p><label></label></p>".PHP_EOL.
             "<p><label>Statement: <br> <textarea>".$details['peopleStatement']."</textarea></label></p>".PHP_EOL.
             "<p><label><input type=\"hidden\" name=\"CMD\" value=\"updateProfile\"></label></p>".PHP_EOL.
             "<p><label><input class=\"button\" type=\"submit\" value=\"update\"></label></p>".PHP_EOL.
             "</form>";
    }
}
/* START 
/* enforce posting method */

if($_SERVER['REQUEST_METHOD'] == "POST") {
    webTools::cleanArray($_POST);
    $ui = new userUI;
    $ui->postHandle();
}
//
/*END*/
?>