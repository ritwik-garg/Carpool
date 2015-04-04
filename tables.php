<?php

//<!-- ********************************** -->
//<!-- DATABASE STRUCTURE -->
//<!-- ********************************** -->

include_once("php_includes/db_conx.php");

$tbl_users = "CREATE TABLE IF NOT EXISTS users (
			 id INT(11) NOT NULL AUTO_INCREMENT,
			fname VARCHAR(16) NOT NULL,
			lname VARCHAR(16) NOT NULL,
			gender ENUM('m','f') NOT NULL,
			 userlevel ENUM('a','b','c') NOT NULL DEFAULT 'a',
			  email VARCHAR(255) NOT NULL,
			  password VARCHAR(255) NOT NULL,
			   signup DATETIME NOT NULL,
			  lastlogin DATETIME NOT NULL,
			 notescheck DATETIME NOT NULL,
			   activated ENUM('0','1') NOT NULL DEFAULT '0',
			PRIMARY KEY (id)
			 
             )";
$query = mysqli_query($connection, $tbl_users);
if ($query === TRUE) {
	echo "<h3>user table created OK :) </h3>"; 
} else {
	echo "<h3>user table NOT created :( </h3>"; 
}

////////////////////////////////////
$carpool="CREATE TABLE IF NOT EXISTS carpool(
                             userid INT(11) NOT NULL,
                             carpool_id INT(11) NOT NULL AUTO_INCREMENT,
                             source VARCHAR(50) NOT NULL,                           
                             fuel ENUM('p','d') NOT NULL, 
	 car_name VARCHAR(20) NOT NULL,
                             available seats INT(10) NOT NULL,
                             capacity INT(10) NOT NULL,
                             PRIMARY KEY(carpool_id)
                    )";
$query = mysqli_query($connection, $carpool);
if ($query === TRUE) {
	echo "<h3>carpool table created OK :) </h3>"; 
} else {
	echo "<h3>carpool table NOT created :( </h3>"; 
}

////////////////////////////////////
$requests = "CREATE TABLE IF NOT EXISTS requests( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                pool VARCHAR(16) NOT NULL,
                user VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL, 
	accepted ENUM('0','1') DEFAULT '0',  
                PRIMARY KEY (id)
                )"; 
$query = mysqli_query($db_conx, $requests); 
if ($query === TRUE) {
	echo "<h3>requests table created OK :) </h3>"; 
} else {
	echo "<h3>requests table NOT created :( </h3>"; 
}

////////////////////////////////////
$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                user INT(10) NOT NULL,
	receiver INT(10) NOT NULL,
                pool INT(10) NOT NULL,
                type ENUM('0','1','2'),		//0 for accepted; 1 for rejected; 2 for quit
                date_time DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_notifications); 
if ($query === TRUE) {
	echo "<h3>notifications table created OK :) </h3>"; 
} else {
	echo "<h3>notifications table NOT created :( </h3>"; 
}
$db_conx->close();
?>