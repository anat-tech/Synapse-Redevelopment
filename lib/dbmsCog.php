<?php
/** 
  * @name mysql DBMS Cog
  * @author Cameron Milton cameron@anat.org.au
  * @copyright Australian Network for Art and Technology 27th May 2011
  * @version 1.0.1
  * 
**/
class dbmsCog
{
    /* header declarations */
    protected $mysql_username = "synapse_user";
    protected $mysql_pass = "anatpass";
    protected $database = "synapseR";
    protected $link;
    protected $sqlErr;
    protected $rowCount;
    
    /* for making connections to the database */
    private function connect()
    {
        $this->link = mysql_connect ('localhost', $this->mysql_username, $this->mysql_pass);
        /* returns if the connection was made, not the resource itself */
        if($this->link)
        {
            /* connects to specific database */
            $db_select = mysql_select_db($this->database, $this->link);
            if( $db_select)
            {
                return true;
            }
            /* failed to connect to database */
            else
            {
                return false;
                $this->sqlErr = mysql_error();
            }
        }
        /* failed to connect to dbms server*/
        else
        {
            $this->sqlErr = mysql_error();
            return false;
        }
    }
    
    /* for closing connections to the database */
    private function disconnect()
    {
           return mysql_close($this->link);
    }
    
    /* function for executing queries on the dbms */
    private function query($query)
    {
        /* make connection */
        if($this->connect())
        {       
            /* sanitize query */
            $query = stripslashes(mysql_real_escape_string($query, $this->link));
            /* run query*/
            $result = mysql_query($query, $this->link);
            /* 400 Bad Request (probably wrong syntax) */
            if(!$result)
            {
                $this->sqlErr = mysql_error();
                return 400; //Query failed";
            }
            /* no errors */
            else
            {
                //print_r($result);
                $this->rowCount = (mysql_num_rows($result));
                return $result;
            }
            $this->disconnect();
        }
        else return 503; //, database connection failed";
    }
    
    /* for deleting a row */
    function delete($table, $condition)
    {
        /* require these parameters*/
        if(isset($table) && isset($condition))
        {
            $outcome = $this->query("DELETE FROM ".$table." ".$condition);
            if($outcome == 1)
                return 200;
            else
                return $outcome;
        }
        else
        {
            return 406; //not acceptable
        }
    }
    
    /* getter method for sqlError & rowcount */
    function getError() { return $this->sqlErr; }
    function getRowCount() { return $this->rowCount; }
    
    /* for inserting a row */
    function insert($table, $insert)
    {
        /* require these parameters*/
        if(isset($table) && isset($insert))
        {
            /* put incomming variables into insertion query */
            $values = (implode('\',\'',array_values($insert)));
            $keys = array_keys($insert);      
            /* run query */
            $outcome = $this->query('INSERT INTO `'.$table.'` (`'.implode('`,`', $keys).'`) VALUES (\''.$values.'\')');
            /* on success */
            if($outcome == 1)  return 200;       
            /*on fail*/
            else return $outcome;
        }
        else return 406; //Not Acceptable
    }
    
    function recordExists($table, $colum, $value) {
        $result = $this->query("SELECT * FROM ".$table." WHERE ".$colum."='".$value."'");
        $rows = mysql_num_rows($result);        
        if($rows) return true ;
        else return false;
    }
    
    /* select function */
    function select($table, $colums, $conditions)
    {
        /* require these parameters*/
        if(isset($table) && isset($colums))
        {
            $query = "SELECT ".$colums." FROM ".$table;
            /* checks if conditions are set and appends them if so */
            if(isset($conditions)) $query .= " ".$conditions;
            $query .= ";";
            /* run query*/ 
            $outcome = $this->query($query);
            //print_r(mysql_fetch_assoc($outcome));
            if ($this->rowCount == 1) {
                return mysql_fetch_assoc($outcome);
            }
            else if($this->rowCount > 0) {
                return ($outcome);
            }
            else {
                return 404;
            } 
        }
        else {
            return 406; //Not Acceptable
        }
    }
    
    /* update function*/
    function update($table, $colum, $value, $condition)
    {
        /* require these parameters*/
        if( isset($table) && isset($colum)  && isset($value)  && isset($condition))
        {
            $query = ("UPDATE ".$table." SET ".$colum."='".$value."' ".$condition);
            $outcome = $this->query($query);
            /* on success */
            if($outcome == 1)  return 200;       
            /*on fail*/
            else {
                return $outcome;
            }
        }
        else return 406; //Not Acceptable
    }
    
    /* multiple colum up*/
    function updateCols($table,$arry,$condition) {
        if((is_array($arry))  && (isset($condition)) && (isset($table))) {
            $query = "UPDATE ".$table." SET ";
            foreach($arry as $colum => $value) {
                $query .= $colum."=\"".$value."\",";
            }
            $query = substr($query, 0, -1); //trim off the last commar
            $query.= " ".$condition;
            $outcome = $this->query($query);
            if ($outcome == 1) return 200;
            else return $outcome;
        }
    }
}
?>