<hr/>
<?php
  //error_reporting(E_ALL);
  include("user.php");
  //include('dbmsCog.php');
  $ppl = new user();
  
  //echo $ppl->register("cameron", "milton", "cameron2@anat.org.au");
  
  echo $ppl->updatePassword("hi", "cameron@anat.org.au");
  echo $ppl->authenticate("cameron@anat.org.au", "hi");
  //echo $ppl->listPeople();

  //include_once("dbmsCog.php");
  //$dbmsC = new dbmsCog();
  /*echo $dbmsC->insert( 'regions', array(
        'name' => 'Adelaide South Australia',
        'description' => 'Home of ANAT',
        )
  );*/
  //$dbmsC->update("regions", "name", "Brisbane, Queensland", "WHERE id=11");
  
  //$result = $dbmsC->select("regions", "*", "WHERE 1=1");

  //echo $result;
  /*while ($row = mysql_fetch_assoc($result)) {
        echo $row['name']."<br/>";
        echo "<br>";
  }*/
  //echo $dbmsC->delete("regions", "WHERE id=7 OR id=6");

?>
<hr/>

