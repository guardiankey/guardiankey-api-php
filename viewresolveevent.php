<?php

require_once("guardiankey.class.php");

$GK = new guardiankey(null);

$eventid= $_GET['e'];
$token= $_GET['t'];

if ($_POST) {
	
	if ($_POST['know'] == "Yes") {
		$GK->resolveEvent($eventid,$token,'GOOD');
		echo "Thank You!";
	} elseif ($_POST['know'] == "No") {
		$GK->resolveEvent($eventid,$token,'BAD');
		echo "Thank You. Please contact System Administrator!!";
	} else {
		echo "Invalid response!";
	}

	
} else {

$returned = $GK->getEvent($eventid,$token); 

if ($returned->return) {
	echo "Invalid token";
} else {
	echo '
	<h2>Please view the access below:</h2>
	<table align="center">
  <tr>
    <th>Username</th>
    <th>'.$returned->USERNAME.'</th>
  </tr>
  <tr>
    <td>Location</td>
    <td>'.$returned->LOCATION.'</td>
  </tr>
  <tr>
    <td>IP Address</td>
    <td>'.$returned->IPADDRESS.'</td>
  </tr>
  <tr>
    <td>System</td>
    <td>'.$returned->SYSTEM.'</td>
  </tr>
  <tr>
    <td>Date</td>
  <td>'. gmdate("Y-m-d\ H:i:s\ ", $returned->DATETIME).'UTC</td>
  </tr>
</table>
 <p>Do you know this access?</p>
 <form method=post>
 <input type=submit name=know value=Yes>&nbsp<input type=submit name=know value=No>
 </form>';
 
}
}


?>

