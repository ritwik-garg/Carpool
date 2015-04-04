<?php 

//<!-- ********************************** -->
//<!--PAGE TO DISPLAY AVAILABLE CARPOOLS -->
//<!-- ********************************** -->



include_once("check_login_status.php");
if($user_ok == false)
	header("location:signup.php");
$userid = $_SESSION["userid"];
?>
	
<html>
<head>

<style>

#section
{
width :57%;
height :500px;
border-radius: 5px;
font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Geneva, Verdana, sans-serif;
float:left;
margin:0.5%;
text-align:center;
}

table {
border-collapse: collapse;
width:85%;
background: -webkit-linear-gradient(#6600CC, #99CCFF ); /* For Safari 5.1 to 6.0 */
background: -o-linear-gradient(#6600CC, #99CCFF); /* For Opera 11.1 to 12.0 */
background: -moz-linear-gradient(#6600CC, #99CCFF); /* For Firefox 3.6 to 15 */
background: linear-gradient(#6600CC, #99CCFF); /* Standard syntax */
margin-top:9%;
}
th{

border: 2px solid black;
background-color:#52005C;
color:white;
}
tr{
border:1px solid black;
}
td{
border:1px solid black;
}	

</style>
<link rel="stylesheet" href="style/style.css">
<title>Available Carpools</title>
</head>

<body style="margin:0; padding:0; height:700px; background-color:#E0E0E0; text-align:center; ">
<?php include_once("templates/template_header.php"); ?>
<?php include_once("templates/nav.php"); ?>

<div id="sectionhead">
<h2 style="color:#E65940;">Available Carpools</h2>
<br><br><br><br>
</div>

<?php include_once("templates/optionhead.php"); ?>

<br><br><br>
<form name="join_carpool" method="POST" action="viewpool.php">


<?php

echo '<table id="pool" align="center">';
echo '<tr>';
echo '<th>Checkbox</th>';
echo '<th>Source</th>';
echo '<th>Capacity</th>';
echo '<th>Fuel</th>';
echo '<th>Car Name</th>';
echo '<th>Available Seats</th>';
echo '</tr>';

$query = mysqli_query($db_conx,"SELECT * FROM carpool where available_seats>0");
while($result=mysqli_fetch_array($query))
{
	echo '<tr>';
	echo '<td><center><input type="radio" name="carpool" value="'.$result["carpool_id"].'"></center></td>';
	echo '<td style="text-align:center;">'.$result["source"].'</td>';
	echo '<td style="text-align:center;">'.$result["capacity"].'</td>';
	if($result["fuel"]=='p')
		$t="Petrol";
	else
		$t="Diesel";
	echo '<td style="text-align:center;">'.$t.'</td>';
	echo '<td style="text-align:center;">'.$result["car_name"].'</td>';
	echo '<td style="text-align:center;">'.$result["available_seats"].'</td>';
	echo '</tr>';
}
echo '</table>';
?>
<br>
<br>
<input type="submit" value="View Details" align="centre" >
</form>
</body>
</html>
