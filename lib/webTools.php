<?php
/**
 * Description of webTools
 * @author cameron
 * Just a collection of tools to help with processing of user data, etc
 */
if((class_exists('webTools'))) return; //exit if class exists
class webTools {
    public static function cleanArray() {
        foreach ($arr as $key => $value) {
            // remove tags
            $arr[$key] = strip_tags($value);
        }
    }
    
   /* gets the current page url (useful for form actions)
    * taken from: http://www.webcheatsheet.com/PHP/get_current_page_url.php
    */
    public static function currentURL() {
        $pgurl = "http";
        if(!empty($_SERVER['HTTPS'])) $pgurl .= "s";
        $pgurl .= "://".$_SERVER["SERVER_NAME"];
        if ($_SERVER["SERVER_PORT"] != "80") $pgurl .= ":".$_SERVER["SERVER_PORT"];
        
        return ($pgurl .= $_SERVER["SCRIPT_NAME"]);
    }
    
    public static function phpErrorsOn() {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    }
    
    public static function printCookies() {
        print_r($_COOKIE);
    }
}
?>