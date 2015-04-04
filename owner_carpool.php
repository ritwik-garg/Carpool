<?php 

//<!-- ********************************** -->
//<!-- PAGE TO DISPLAY DETAILS FOR OWNER OF A CARPOOL -->
//<!-- ********************************** -->


include_once("check_login_status.php");


//REDIRECTS TO SIGN UP PAGE IF USER IS LOGGED OUT
if($user_ok == false)
	header("location:signup.php");


$userid = $_SESSION["userid"];

$query = mysqli_query($db_conx, "SELECT * FROM carpool where userid = $userid");
$r = mysqli_num_rows($query);
$result = mysqli_fetch_array($query);  

if(isset($_POST["action"])) //checks to delete
{
	$a=$_POST["action"];
	if($a=="change")
	{ 	$query = mysqli_query($db_conx, "SELECT * FROM carpool_$userid");
		$num = mysqli_num_rows($query);
		$sql=mysqli_fetch_array(mysqli_query($db_conx,"SELECT * FROM carpool WHERE userid=$log_id"));
		$carpool=$sql["carpool_id"];
		if($num>0)
		{	$sql=mysqli_query($db_conx,"DELETE FROM notifications WHERE pool=$carpool");
		 while($result = mysqli_fetch_array($query))
		{
			$tmp = $result["userid"];
			if($tmp!=$userid)
			{
			$sql=mysqli_query($db_conx,"INSERT INTO notifications(user,receiver,type,pool,date_time) VALUES ('$log_id','$tmp','3','$carpool',now())");
			}
		}
		$sql=mysqli_query($db_conx,"DROP TABLE carpool_$log_id");
		$sql=mysqli_query($db_conx,"DELETE FROM requests WHERE pool=$carpool");
		$sql=mysqli_query($db_conx,"DELETE FROM carpool WHERE userid=$log_id");
		echo "deleted";
		exit();
	}else echo "error";
	}
	else echo "error";
}
?>


<html>
<head>
<link rel="stylesheet" href="style/style.css">	
<title>Carpool Owner</title>
<style>
#left
{
padding-right: 6%;
float:left;
text-align:right;
color: #330066;
width:44%;
}
#right
{
color: #6633CC ;
width:44%;
float:left;
text-align:left;
padding-left: 6%;
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function delcar()
{		var conf = confirm("Are you sure you want to delete this carpool?");
		if(conf != true){
			return false;
			}
		var action= _("deletebtn").value;
		_("deletebtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "owner_carpool.php");
		ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "deleted"){
				window.location = "make_carpool.php";
			} 
			else   _("status").innerHTML = ajax.responseText;//_("status").innerHTML = "Delete unsuccessful, please try again.";
		}
	}
	ajax.send("action="+action);
}
</script>
</head>

<body style="margin:0; padding:0; height:700px; background-color:#E0E0E0 ">
<?php include_once("templates/template_header.php"); ?>
<?php include_once("templates/nav.php");  ?>

<div id="sectionhead">
<h2 text-align:center;> 
<?php 
$resu = mysqli_fetch_array(mysqli_query($db_conx, "SELECT * FROM users WHERE id=$userid"));
echo $resu["fname"]." ".$resu["lname"];
?>
's Carpool
</h2>
</div>

<?php include_once("templates/optionhead.php"); ?>

<?php include_once("templates/navd.php");  ?>

<div id="section">
<div id="left">
<br>
Source :  <br><br>
	
Car Name :  <br><br>
	
Capacity :  <br><br>
	
Fuel Type :  <br><br>
                  
Seats Available :  <br><br>	
</div>

<div id="right">
<br>
<?php echo $result["source"] ?>  <br><br>
<?php echo$result["car_name"];?> <br><br>
<?php echo$result["capacity"];?> <br><br>
<?php if($result["fuel"]=='p')
		$t="Petrol";
	else
		$t="Diesel"; ?>
	<?php echo $t;  ?> <br><br>
<?php echo$result["available_seats"] .'<br>';?>  <br>
</div>

	Following users are in your carpool :- <br><br>
<?php 
$tmp = $result["userid"];
	$query = mysqli_query($db_conx, "SELECT * FROM carpool_$tmp");
	$num = mysqli_num_rows($query);
	if($num == FALSE)
		echo 'No one has joined your carpool. Invite your friends!!!';
	else
	{
		 while($result = mysqli_fetch_array($query))
		{
			$tmp = $result["userid"];
			$res = mysqli_fetch_array(mysqli_query($db_conx, "SELECT * FROM users WHERE id=$tmp"));
			echo '<a href="profile.php?userid='.$tmp.'">'. $res["fname"]." ".$res["lname"].'</a><br> ';
		}
	}
?>
<form onsubmit="return false" method="post"><br><button id="deletebtn" value="change" onclick="delcar()">Delete Carpool</button><span id="status"></span></form>
</div>

<?php include_once("templates/template_options.php");  ?>

</body>
</html>