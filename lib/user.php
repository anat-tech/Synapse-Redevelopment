<?php
/* User class
 * Use Cases: Login/Authentication, updatepassword, lostpassword, update information.
 * 
 */

require_once('dbmsCog.php');

class user
{
    protected $dbmsC;
    
    public function __construct()
    {
        $this->dbmsC = new dbmsCog();
    }
    /* registerting people into the database */
    function register($fname, $lname, $email) {    
        // check email does not exist 
        if($this->dbmsC->recordExists("people", "email", $email)) {
            return 409; //conflict - record exists
        }     
        /* generate password */
        $pass = $this->generatePassword(8);
        /*generate salt */
        $salt = $this->generatePassword(rand(6,254));   
        //$pass = sha1($salt.$pass);
        $profile = array("firstname"=> $fname, "lastname"=>$lname, "email"=>$email, "salt"=>$salt, "passwd"=>sha1($salt.$pass));
        /*insert data*/
        $this->dbmsC->insert("people", $profile );
        
        /*send email*/
        mail($email, "Welcome to Synapse", "username: ".$email.PHP_EOL."password: ".$pass);
        /*debugging take a log */
        //file_put_contents("reg.log", "email: ".$email.", pass: ".$pass , FILE_APPEND);
        echo "</h4>".$pass."<h4>";
        
        return $profile;
    }
    
    function adjustCookie($email, $pass, $ip) {
        $check = $this->dbmsC->select("people", "cookiehash", "where");
        if((isset($pass)) && ($check == NULL)) {
            echo "set cookie";
        }
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
    protected function generatePassword($digit) {
        $out = "";
        for ($digit = 0; $digit < 6; $digit++) {
            $out .= chr(rand(65,122));
        }
        return $out;
    }
    
    /* hash and salt password, assumes salt is in database. */
    protected function hashAndSaltPword($pword, $email) {
        $salt = $this->dbmsC->select("people", "email, salt", "WHERE email='".$email."'");
        //$salt = mysql_fetch_assoc($salt);
        if($salt) {
        echo $salt;
        //$salt = $salt['salt'];
        $pword = sha1($salt.$pword);
        }
        else echo "blargh";
        return $pword;
    }
    
    /* compares the hashed password against the stored hash */
    function authenticate($email, $passwd_in) {
        /*generates password*/
        $passwd_in = $this->hashAndSaltPword($passwd_in, $email);
        /* grabs stored password*/
;
        $passwd = $this->dbmsC->select("people", "email, passwd", "where email=\"".$email."\"");
        echo "passwd";
        //$passwd = mysql_fetch_assoc($passwd);
       // $passwd = $passwd['passwd'];
        
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