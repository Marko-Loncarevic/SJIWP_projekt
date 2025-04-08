<?php
$server='localhost';
$username='root';
$password='';
$database='rentacar';

$db = mysqli_connect($server, $username, $password);
if($db)
{ 
  // echo 'uspjesno ste povezani ';
$db_selected = mysqli_select_db($db, $database);
if(!$db_selected){
 //echo 'Doslo je do pogreske kod odabira baze';
}
//else{ echo'uspjesno je spojena';}
}
//echo 'Doslo je do pogreske prilikom spajanja';

?>




