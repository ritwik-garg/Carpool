<?php
require_once("../php_includes/db_conx.php"); //.. to move up one folder
// This block deletes all accounts that do not activate after 3 days
$sql = "SELECT id, email FROM users WHERE signup<=CURRENT_DATE - INTERVAL 3 DAY AND activated='0'";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if($numrows > 0){
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
	  $id = $row['id'];
	  $email = $row['email'];
	  $userFolder = "../user/$email";
	  if(is_dir($userFolder)) {
          rmdir($userFolder);
      }
	  mysqli_query($db_conx, "DELETE FROM users WHERE id='$id' AND email='$email' AND activated='0' LIMIT 1");
	  mysqli_query($db_conx, "DELETE FROM useroptions WHERE email='$email' LIMIT 1");
    }
}
?>