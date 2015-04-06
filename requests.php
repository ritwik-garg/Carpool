<?php

//<!-- ********************************** -->
//<!-- REQUEST MECHANISM TO JOIN OR QUIT A CARPOOL -->
//<!-- ********************************** -->

include_once("check_login_status.php");
if($user_ok != true || $log_id == "") {
	header("location:signup.php");
}
?><?php
if (isset($_POST['type']) && isset($_POST['id'])){
	$id = preg_replace('#[^a-z0-9]#i', '', $_POST['id']);
	$sql = "SELECT carpool_id FROM carpool WHERE carpool_id='$id' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$count = mysqli_num_rows($query);
	if($count < 1){
		mysqli_close($db_conx);
		echo "Carpool does not exist.";
		exit();
	}
	if($_POST['type'] == "join"){
		$sql = "SELECT COUNT(id) FROM requests WHERE user='$log_id' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count3 = mysqli_fetch_row($query);
		$sql = "SELECT COUNT(id) FROM requests WHERE pool='$id' AND accepted='1' ";
		$query = mysqli_query($db_conx, $sql);
		$member_count = mysqli_fetch_row($query);
		$sql = "SELECT COUNT(id) FROM requests WHERE pool='$id' AND user='$log_id' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count1 = mysqli_fetch_row($query);
		$sql = "SELECT COUNT(id) FROM requests WHERE pool='$id' AND user='$log_id' AND accepted='0' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count2 = mysqli_fetch_row($query);
	if ($row_count3[0] > 0) {
		    mysqli_close($db_conx);
	        echo "You must leave your current car pool to send this request.";
	        exit();
	    } else if($member_count[0] >= (mysqli_query($db_conx, "SELECT capacity FROM carpool WHERE carpool_id='$id' LIMIT 1"))){
            mysqli_close($db_conx);
	        echo "This carpool currently has the maximum number of passengers, and cannot accept more.";
	        exit();
        } else if ($row_count1[0] > 0) {
		    mysqli_close($db_conx);
	        echo "You are already a member of this pool.";
	        exit();
	    } else if ($row_count2[0] > 0) {
		    mysqli_close($db_conx);
	        echo "You have a pending request sent to the car pool";
	        exit();
	    } else {
	        $sql = "INSERT INTO requests(pool, user, datemade) VALUES('$id','$log_id',now())";
		    $query = mysqli_query($db_conx, $sql);
			mysqli_close($db_conx);
	        echo "request_sent";
	        exit();
		}
	} else if($_POST['type'] == "quit"){
		$sql = "SELECT COUNT(id) FROM requests WHERE pool='$id' AND user='$log_id' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count = mysqli_fetch_row($query);
	    if ($row_count[0] > 0) {
	        $sql = "DELETE FROM requests WHERE user='$log_id' AND pool='$id' AND accepted='1' LIMIT 1";
			$query = mysqli_query($db_conx, $sql);
			$res = mysqli_fetch_array(mysqli_query($db_conx, "SELECT userid FROM carpool WHERE carpool_id=$id"));
			$owner=$res["userid"];
			$sql=mysqli_query($db_conx,"INSERT INTO notifications(user,receiver,type,pool,date_time) VALUES ('$log_id','$owner','2','$id',now())");
			$result = mysqli_query($db_conx, "DELETE FROM carpool_$owner WHERE userid=$log_id");
			$result= mysqli_query($db_conx, "DELETE FROM requests WHERE user=$log_id");
			$result = mysqli_query($db_conx, "UPDATE carpool SET available_seats = available_seats+1 WHERE carpool_id = $id");
			mysqli_close($db_conx);
	        echo "quit_ok";
	        exit();
	    } else {
			mysqli_close($db_conx);
	        echo "You are not a member of the pool $id, therefore you can\'t quit.";
	        exit();
		}
	}
}
?><?php
if (isset($_POST['action']) && isset($_POST['reqid']) && isset($_POST['carpool_id']) && isset($_POST['user'])){
	$reqid = preg_replace('#[^0-9]#', '', $_POST['reqid']);
	$action = $_POST['action'];
	$id = preg_replace('#[^0-9]#', '', $_POST['carpool_id']);		//carpool id
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST['user']);
	$sql = "SELECT COUNT(id) FROM users WHERE id='$user' LIMIT 1"; // AND activated='1'
	$query = mysqli_query($db_conx, $sql);
	$exist_count = mysqli_fetch_row($query);
	if($exist_count[0] < 1){
		mysqli_close($db_conx);
		echo "$user does not exist.";
		exit();
	}
	if($action == "accept"){
		$sql = "SELECT COUNT(id) FROM requests WHERE user='$user' and pool='$id' AND accepted='1' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
		$row_count = mysqli_fetch_row($query);
	    if ($row_count[0] > 0) {
		    mysqli_close($db_conx);
	        echo "User already a member of this pool";
	        exit();
	    } else {
			$query=mysqli_fetch_array(mysqli_query($db_conx,"SELECT available_seats FROM carpool WHERE carpool_id=$id"));
			if($query["available_seats"]<1){
				echo "Maximum members in your carpool. Please delete a few to add new members."; 
				exit(); }			
			$query = mysqli_query($db_conx,"UPDATE requests SET accepted='1' WHERE  user='$user' AND  pool='$id' LIMIT 1");	
			/*$res = mysqli_fetch_array(mysqli_query($db_conx, "SELECT userid FROM carpool WHERE carpool_id=$id"));
			$carpool_userid=$res["userid"];*/
			$result = mysqli_query($db_conx, "INSERT INTO carpool_$log_id VALUES ($user)");
			$result = mysqli_query($db_conx, "UPDATE carpool SET available_seats = available_seats-1 WHERE userid = $log_id");
			$sql=mysqli_query($db_conx,"INSERT INTO notifications(user,receiver,type,pool,date_time) VALUES ('$log_id','$user','0','$id',now())");
			mysqli_close($db_conx);
	        echo "accept_ok";
	        exit();
		}
	} else if($action== "reject"){
		$sql=mysqli_query($db_conx, "DELETE FROM requests WHERE user='$user' AND pool='$id' AND accepted='0' LIMIT 1");
if($sql==false) echo"oh shit";
		$sql=mysqli_query($db_conx,"INSERT INTO notifications(user,receiver,type,pool,date_time) VALUES ('$log_id','$user','1','$id',now())");
		mysqli_close($db_conx);
		echo "reject_ok";
		exit();
	}

}
?>