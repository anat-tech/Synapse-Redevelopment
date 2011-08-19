<?php
/* User class
 * Use Cases: Login/Authentication, updatepassword, lostpassword, update information.
 * 
 */

require_once('dbmsCog.php');

class user
{
    protected $dbmsC;
    protected $cookiename = "synapse-valve";
    protected $cookietimeout = 3600; //3600 = 1hour
    
    public function __construct()
    {
        $this->dbmsC = new dbmsCog();
    }
    /* registerting people into the database */
    function register($fname, $lname, $email,$pass) {    
        // check email does not exist 
        if($this->dbmsC->recordExists("people", "email", $email)) {
            return 409; //conflict - record exists
        }
        if(!(isset($pass))) {
            /* generate password */
            $pass = $this->generatePassword(8);
        }
            /*generate salt */
            $salt = $this->generatePassword(rand(6,254));   
            
       $profile = array("firstname"=> $fname, "lastname"=>$lname, "email"=>$email, "salt"=>$salt, "passwd"=>sha1($salt.$pass));
       /*insert data*/
       $this->dbmsC->insert("people", $profile );
        
            /*send email*/
            //mail($email, "Welcome to Synapse", "username: ".$email.PHP_EOL."password: ".$pass);
            /*debugging show pass */
        echo "</h4>".$pass."<h4>";
        
        return $profile;
    }
    
    function adjustCookie($email, $pass, $ip) {
        /* authenticate again, just incase */
        if(!($this->authenticate($email, $pass))) return false;
        //gets the current cookie
        $check = $this->dbmsC->select("people", "cookiehash", "where email='".$email."'");
        $check = mysql_fetch_assoc($check);
        $check = $check['cookiehash'];
        
        //gets the cookie stored time
        $scktime = $this->dbmsC->select("people", "cookietime", "where email='".$email."'");
        $scktime = mysql_fetch_assoc($scktime);
        $scktime = $scktime['cookietime'];
        
        /* delete cookie data if cookie has timed out */
        if(time() - $scktime < $this->cookietimeout){
            $this->dbmsC->update("people", "cookiehash", NULL, "where email='".$email."'");
        }
        
        //checks if cookie has been set, if not, create cookie!
        if($check == NULL) {
            /* create random data for the cookie */
            $cookiesalt = $this->generatePassword(rand(8,12));
            
            //generate salted cookie data; note the lack of sensitive information used.
            $saltedcookie = sha1($cookiesalt.$ip.$_SERVER['HTTP_USER_AGENT']);
            
            ///store cookie data in database for verfication later.
            //change these to be one query for optimisation
            if( setcookie($this->cookiename, $saltedcookie, time()+$this->cookietimeout, "/", $_SERVER['SERVER_NAME'])) {
            $this->dbmsC->update("people", "cookiehash", $saltedcookie, "where email='".$email."'");
            $this->dbmsC->update("people", "cookiesalt", $cookiesalt, "where email='".$email."'");
            $this->dbmsC->update("people", "cookietime", time(), "where email='".$email."'"); 
            }
            return 200;
        }
        else if(!($this->checkcookie())) {
            $this->dbmsC->update("people", "cookiehash", NULL, "where email='".$email."'");
            return "Warning: previous sesssion existed, someone may have been logged in as you. Therefore your account has been logged out";
        }
        //if user is logged in already
        return "You were logged in already.";
    }
    
    //this should occur everytime a sensitive page is loaded, thus it should be made very effecient
    function checkCookie() {
        print_r($_COOKIE);
        if(isset($_COOKIE[$this->cookiename])) { //if a cookie is set
        $check = $this->dbmsC->select("people", "cookiehash,cookiesalt", "where cookiehash='".$_COOKIE[$this->cookiename]."'");
        if(($check == 404) || ($check == 406)) return false;
        //if cookie exists, sanity check.
        
        $cookiesalt = mysql_fetch_assoc($check);
        $cookiesalt = $cookiesalt['cookiesalt'];
        return (sha1($cookiesalt.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']) == $_COOKIE[$this->cookiename]);
        }
        return false;
    }
    
    /* Basic people listing */
    function listPeople() {
        //$arry = array();
        $result = $this->dbmsC->select("people", "firstname, lastname, email, url", "WHERE confirmed=1");
        $list;
        while ($row = mysql_fetch_assoc($result)) {
            $list .= $row['firstname'].", ".$row['lastname'].", ".$row['email'].", ".$row['url'].PHP_EOL;
        }
        return $list;
    }
    
    /* function for updating both password and username/email */
    function updateCredentials($oldpass, $passwd, $email, $newemail){
        if ($this->authenticate($email, $oldpass) == 200) {
            /* updates password if there is a new one */
            if(!(empty($passwd))) {
                $this->updatePassword ($passwd, $email);
            }
            /* update email if there is a new one */
            if(!(empty($newemail))) {
                $this->dbmsC->update("people", "email", $newemail, "WHERE email=".$email);
            }
        }
        else {
            return 401; //unauthorised
        }
    }
    
    /* function for updating a password where email=email */
    private function updatePassword($passwd, $email) {
        /* check user exists*/
        if(!($this->dbmsC->recordExists("people", "email", $email))) {
        return 404; //user not found
        }
        else {
        /* hash pass */
        $passwd = $this->hashAndSaltPword($passwd, $email);
        /* update password stored in database*/
        $result = $this->dbmsC->update("people", "passwd", $passwd, "WHERE email=".$email);
        return $result;
        }
    }
 
    /* function for dealing with lost passwords */   
    function resetPassword($email) {
        /* generate password and update user*/
        $pword = generatePassword(rand(8,14));
        // on successfull update
        if($this->updatePassword($pword, $email) == 200)
        {
            return $pword;
            // e-mail user new password
            if(mail($email, "Synapse password reset", "Your new password for synapse is: ".$pword)) return 200;
            else return 409; //conflict
        }
    }
    
    /* generates a random string of characters */
    protected function generatePassword($max) {
        $out = "";
        //0-3 = uppercase, 3-6 = lowercase, 6-12 = numbers
        //50/50 = no bias towards letters or numbers;
        for ($digit = 0; $digit < $max; $digit++) {
            $dice = rand(1,12);
            if( $dice < 3 )
               $out .= chr(rand(65,90));
            else if ($dice < 6){
                $out .= chr(rand(97, 122));
            }
            else { //numbers
                $out .= chr(rand(48,57));
            }
        }
        return $out;
    }
    
    /* hash and salt password, assumes salt is in database. */
    protected function hashAndSaltPword($pword, $email) {
        $salt = $this->dbmsC->select("people", "email, salt", "WHERE email='".$email."'");
        
        $salt = mysql_fetch_assoc($salt);
        if($salt) {
           $salt;
            $salt = $salt['salt'];
            $pword = sha1($salt.$pword);
        }
        return $pword;
    }
    
    /* compares the hashed password against the stored hash */
    function authenticate($email, $passwd_in) {
        /*generates password*/
        $passwd_in = $this->hashAndSaltPword($passwd_in, $email);
        /* grabs stored password*/
        $passwd = $this->dbmsC->select("people", "email,passwd", "where email='".$email."'");
        $passwd = mysql_fetch_assoc($passwd);
        $passwd = $passwd['passwd'];
        
        /* compares */
        if ($passwd == $passwd_in) {
            return 200;
        }
        else {
            return 401; //unauthorized
        }
    }
}
?>