<?php
// SIGNUP AND LOGIN PAGE -->
//<!-- ********************************** -->

include_once("check_login_status.php");
// If user is already logged in, header them away
if($user_ok == true){
	header("location: homepage.php");
    exit();
}

// Ajax calls this EMAIL CHECK code to execute
if(isset($_POST["emailcheck"]))
{
	require_once("php_includes/db_conx.php");
	$email =mysqli_real_escape_string($db_conx, $_POST['emailcheck']);
	$sql = "SELECT id FROM users WHERE email='$email' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $email_check = mysqli_num_rows($query);
    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
	    echo '<strong style="color:#F00;">Invalid email format</strong>';
	    exit();
    }
	if (is_numeric($email[0])) {
	    echo '<strong style="color:#F00;">Email must begin with a letter</strong>';
	    exit();
    }
    if ($email_check < 1) {
	    echo '<strong style="color:#009900;">OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $email . ' already has an account associated with it.</strong>';
	    exit();
    }
}
if(isset($_POST["pass1"])) //checks length of password
{
	$p1=$_POST["pass1"];
	if (strlen($p1) < 6 || strlen($p1) > 16) {
	    echo '<strong style="color:#F00;">6 - 16 characters please</strong>';
	    exit();
	}else { echo ''; exit();}
}
if(isset($_POST["p1"])) // checks if retyped password matches
{
	$p1=$_POST["p1"];
	$p2=$_POST["p2"];
	    if($p1==$p2) {
	    echo '<strong style="color:#009900;">OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">Passwords dont match.</strong>';
	    exit();
    }
}
?><?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["fu"]))
{
	// CONNECT TO THE DATABASE
	require_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$fu = preg_replace('#[^a-z]#i', '', $_POST['fu']);
	$lu = preg_replace('#[^a-z]#i', '', $_POST['lu']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$p2=$_POST['p2'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	// DUPLICATE DATA CHECKS EMAIL
	$sql = "SELECT id FROM users WHERE email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if($fu == "" || $e == "" || $p == ""|| $p2 == "" || $g == "" || $lu == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($e_check > 0){ 
        echo "An account already exists with this email address";
        exit();
	}else if (strlen($p) < 6 || strlen($p) > 16) {
        echo "Password must be between 3 and 16 characters";
        exit(); 
    }else if ($p!=$p2) {
        echo "Passwords do not match";
        exit(); 
    } else  if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$e)) {
	    echo "Invalid email format";
	    exit();
	}else if (is_numeric($e[0])) {
        echo 'Email cannot begin with a number';
        exit();
    } else {
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		$p_hash = md5($p);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO users (fname,lname, email, password, gender, signup, lastlogin) VALUES('$fu','$lu','$e','$p_hash','$g',now(),now())";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$e")) {
			mkdir("user/$e", 0755);
		}
		// Email the user their activation link
		$to = "$e";							 
		$from = "richayadav29@gmail.com";
		$subject = 'DtuKonnect Account Activation';
		$message = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>DtuKonnect Message</title></head><body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;"><div style="padding:10px; background:#333; font-size:24px; color:#CCC;"><a href="http://www.yoursitename.com"></a>yoursitename Account Activation</div><div style="padding:24px; font-size:17px;">Hello '.$fu.',<br /><br />Click the link below to activate your account when ready:<br /><br /><a href="http://www.yoursitename.com/activation.php?id='.$uid.'&fu='.$fu.'&e='.$e.'&p='.$p.'">Click here to activate your account now</a><br /><br />Login after successful activation using your:<br />* E-mail Address: <b>'.$e.'</b></div></body></html>';
		$headers = "From: $from\n";
        $headers = "MIME-Version: 1.0\n";
        $headers = "Content-type: text/html; charset=iso-8859-1\n";
		mail($to, $subject, $message, $headers);
    			/*echo "Email sent";
		else
    			echo "Email sending failed";*/
    		echo 'signup_success';
		exit();
	}
	exit();
}

// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["el"])){
	// CONNECT TO THE DATABASE
	require_once("php_includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$e = mysqli_real_escape_string($db_conx, $_POST['el']);
	$p = md5($_POST['pl']);
	// FORM DATA ERROR HANDLING
	if($e == "" || $p == ""){
		 	echo 'login_failed';
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, email, password FROM users WHERE email='$e' LIMIT 1"; //AND activated='1' add later
        $query = mysqli_query($db_conx, $sql);
	$q=mysqli_num_rows($query);
	if($q<1)
	{ echo 'login_failed'; exit();}
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_email = $row[1];
        $db_pass_str = $row[2];
		if($p != $db_pass_str){
				echo 'login_failed';
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['email'] = $db_email;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("email", $db_email, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE users SET lastlogin=now() WHERE email='$db_email' LIMIT 1"; // ip='$ip',
            $query = mysqli_query($db_conx, $sql);
		    exit();
		}
	}
	exit();
}

?>


<!DOCTYPE HTML>
<html>
<head>
<style>

@-webkit-keyframes mymove {
    from {background-color: #F8F8F8 ;
          color: #101010 ;
            }
    to {background-color: #202020  ;
         color: #E8E8E8 ;
         }
}

@keyframes mymove {
    from {background-color: #F8F8F8 ;
          color: #101010 ;
          }
    to {background-color: #202020  ;
         color: #E8E8E8 ;
         }
}

#top
{
float :left;
background-color: #C0C0C0 ;
opacity: 0.6;
height: 80px;
text-align:center;
width: 100%;
font-family:"ALGERIAN";
font-size: 400%;
-webkit-animation: mymove 7s infinite; /* Chrome, Safari, Opera */
animation: mymove 7s infinite;
margin :5px;
}

#right
{
float :right;
  background: -webkit-linear-gradient(#000000, #C0C0C0 ); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#000000, #C0C0C0); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#000000, #C0C0C0); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#000000, #C0C0C0); /* Standard syntax */
opacity: 0.5;
height: 150px;
width: 28%;
border-radius: 5px;
color: #FF0066;
font-weight: 900;
font-family: Rockwell, 'Courier Bold', Courier, Georgia, Times, 'Times New Roman', serif;
font-size: 18px;
text-align:center;
}

#right:hover
{
background: -webkit-linear-gradient(#000000, #300000  ); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#000000, #300000 ); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#000000, #300000 ); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#000000, #300000 ); /* Standard syntax */
}

#left
{height: 250px;
width: 72%;
}

#rightmost 
{
float :right;
  background: -webkit-linear-gradient(#000000, #C0C0C0 ); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#000000, #C0C0C0); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#000000, #C0C0C0); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#000000, #C0C0C0); /* Standard syntax */
opacity: 0.5;
height :400px;
width :28%;
border-radius: 5px;
font-family: 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Geneva, Verdana, sans-serif;
font-size: 16px;
text-align:center;
color: #FF3D66;
font-weight: 900;
}

#rightmost:hover
{
background: -webkit-linear-gradient(#000000, #300000  ); /* For Safari 5.1 to 6.0 */
  background: -o-linear-gradient(#000000, #300000 ); /* For Opera 11.1 to 12.0 */
  background: -moz-linear-gradient(#000000, #300000 ); /* For Firefox 3.6 to 15 */
  background: linear-gradient(#000000, #300000 ); /* Standard syntax */
}

.hide
{
	display:none;
}
.hide_1
{
        display:none; 
}
</style>

<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function restrict(elem)
{
	var tf = _(elem);
	var rx = new RegExp;
	if(elem == "email"){
		rx = /[' "]/gi;
	} else if(elem == "fname"){
		rx = /[^a-z]/gi;
	}else if(elem == "lname"){
		rx = /[^a-z]/gi;
	}
	tf.value = tf.value.replace(rx, "");
}
function emptyElement(x)
{
	_(x).innerHTML = "";
}
function checkemail()
{
	var e = _("email").value;
	if(e != ""){
		_("emailstatus").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("emailstatus").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("emailcheck="+e);
	}
}
function checkpass()
{
	var p1 = _("pass1").value;
	var p2 = _("pass2").value;
	if(p2 != ""){
		_("passstatus").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("passstatus").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("p1="+p1+"&p2="+p2);
	}
}
function passlen()
{
	var p1 = _("pass1").value;
	if(p1 != ""){
		_("passlen").innerHTML = 'checking ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            _("passlen").innerHTML = ajax.responseText;
	        }
        }
        ajax.send("pass1="+p1);
	}
}
function signup()
{
	var fu = _("fname").value;
	var lu = _("lname").value;
	var e = _("email").value;
	var p1 = _("pass1").value;
	var p2 = _("pass2").value;
	var g = _("gender").value;
	if(fu == "" || e == "" || p1 == "" || p2 == "" || lu == "" || g == ""){
		_("status").innerHTML = "Fill out all of the form data";
	} else {
		_("signupbtn").style.display = "none";
		_("status").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	           if(ajax.responseText != "signup_success")
				{
					_("status").innerHTML = ajax.responseText;
					_("signupbtn").style.display = "block";
				} else
                                                                          {
					window.scrollTo(0,0);
					_("signupform").innerHTML = "OK "+fu+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.";
				}
	        }
        }
        ajax.send("fu="+fu+"&lu="+lu+"&e="+e+"&p="+p1+"&p2="+p2+"&g="+g);
	}
}

function login(){
	var el = _("lemail").value;
	var pl = _("lpassword").value;
	if(el == "" || pl == ""){
		_("logstatus").innerHTML = "Fill out all of the form data!";
	} else {
		_("loginbtn").style.display = "none";
		_("logstatus").innerHTML = 'please wait ...';
		var ajax = ajaxObj("POST", "signup.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
			            if(ajax.responseText == "login_failed"){
					_("logstatus").innerHTML = "Login unsuccessful, please try again.";
					_("loginbtn").style.display = "block";
				} else {
					window.location = "homepage.php";
				}
	        }
        }
        ajax.send("el="+el+"&pl="+pl);
	}
}

</script>
<title>DTU Konnect</title>
</head>

<body>
<video autoplay loop id="bgvid"
style="position: fixed; right: 0; bottom: 0;
min-width: 100%; min-height: 100%;
width: auto; height: auto; z-index: -100;
background: url(C:\Users\Admin\Desktop\Greentipcarpooling.mp4) no-repeat;
background-size: cover;" muted>
<source src="Greentipcarpooling.mp4" type="video/mp4">
</video>

<div id="top"> 
   DTU KONNECT
</div>
  
 <div id="right">
  
 <!-- LOGIN FORM -->
   <form id="loginform" name= "loginform" input type="text" name="Page" value="login" onsubmit="return false" action="signup.php" method="post">
   <br><label for="lemail">Email:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
   <input type="text" id="lemail" onfocus="emptyElement('status')" maxlength="88" name="lemail" placeholder="Email" autocomplete="on"><br><br>
   <label for="password">Password:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
    <input type="password" id="lpassword" onfocus="emptyElement('status')" maxlength="100" name="lpassword" placeholder="Your Secret Syllabe" ><br><br>
   <button id="loginbtn" onclick="login()">Log In</button> <span id="logstatus"></span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
    <a href="#">Forgot Your Password?</a>
   </form>
</div>

<div id="left">
</div>

<div id="rightmost">
<h2>NEW &nbsp TO &nbsp DTU &nbsp KONNECT?</h2>
<h3>Register here</h3><br>

 <!-- SIGNUP FORM -->

<form name="signupform" id="signupform" onsubmit="return false" input type="text" value="signin" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
<label for="fname">First Name:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
<input id="fname" type="text" name="fname" onkeyup="restrict('fname')" maxlength="16" required><br><div style="line-height:50%;">
    <br>
</div>
<label for="lname">Last Name:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
<input id="lname" type="text" name="lname" onkeyup="restrict('lname')" maxlength="16" required><br><div style="line-height:50%;">
    <br>
</div>
<label for="email">EMAIL:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
<input id="email" type="text" onblur="checkemail()" onfocus="emptyElement('status')" onkeyup="restrict('email')" maxlength="88" name="email" required><span id="emailstatus"></span><br><div style="line-height:50%;">
    <br>
</div>
<label for="gender">Gender:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp </label>
<select id="gender" onfocus="emptyElement('status')">
      <option value=""></option>
      <option value="m">Male</option>
      <option value="f">Female</option>
    </select>
<br><div style="line-height:50%;">
    <br>
</div>
<label for="password">Password:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</label>
<input id="pass1" type="password" onblur="passlen()"onfocus="emptyElement('status')" maxlength="16" name="pass1" reqiured><span id="passlen"></span><br><div style="line-height:50%;">
    <br>
</div>
<label for="rpassword">Retype Password:&nbsp&nbsp&nbsp&nbsp</label>
<input id="pass2" type="password" onblur="checkpass()" onfocus="emptyElement('status')" maxlength="16" name="pass2" reqiured><span id="passstatus"></span> <br><br>
<button id="signupbtn" onclick="signup()">Create Account</button> <span id="status"></span>
</form>

<!-- SIGNUP FORM -->

</div>

</body>
</html>
