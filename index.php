<?php

session_start();

if(isset($_SESSION["logged_in"]))	{
	if($_SESSION["logged_in"])	{
		header("location:dashboard.php");
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Home | Online portal to stay connected with peers.</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours." >
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/login.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">

</head>
<body>

<?php #include "header.php"; ?>

<?php
include "connectDb.php";
$message=$pwddb="";
$userid="";
$nameError="*";
$nameSuc="";
$pwdError="*";
$checked=$success=true;
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	#check for empty field
	if(empty($_POST["userid"]))	{
		$message="Enter mandatory fields";
		$checked=false;
	}
	else	{
		$userid=processData($_POST["userid"]);
	}
	#check for empty field.
	if(empty($_POST["pwd"]))	{
		$message="Enter mandatory fields";
		$checked=false;
	}
	else	{
		$pwd=processData($_POST["pwd"]);
	}
	
	if($checked==true)	{
		try	{
			$sql="select encrypt_pwd from users where user_id='".$userid."'";
			$stmt=$conn->prepare($sql);
			$stmt->execute();
			$row=$stmt->fetch();
			
			$pwddb=$row['encrypt_pwd'];
			
			#check is user entered the correct password.
			if(md5($pwd)!=$pwddb)	{
				$message="Invalid credentials";
				$success=false;
			}
			
			#Redirect to dashboard welcome page for correct credentials.
			if($success==true)	{
				include "session.php";	#start user session
				header("location:dashboard.php");
			}
		}
		catch(PDOException $e)	{
			$message= "Error occurred : ".$e->getMessage();
		}
	}
}
#Function to trim extra spaces/backslashes and avoiding cross-scripting
function processData($text)	{
	$text=trim($text);
	$text=stripslashes($text);
	$text=htmlspecialchars($text);
	return $text;
}
?>
<div id="bg-window"></div>
	<!--<div class="container"> -->
		<div id="main-container">
			</br>
			<div>
				<img src="img/logo.jpg" width="250" height="60"/>
			</div>
			<span id="err1"><?php echo $message; ?></span></br></br>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				Username : <input class="input-class" id="name-area" type="text" name="userid" placeholder="Enter login ID" value="<?php echo $userid;?>" /></span></br></br>
				Password : <input class="input-class" id="pwd-area" type="password" name="pwd" placeholder="Enter password" /></br></br></br>
			<!--	<div class="g-signin2" data-onsuccess="onSignIn"></div></br> -->
				<input type="checkbox" name="remMe">Remember me
				&emsp;<a id="fp-link" href="frgt_pwd.php" target="_blank" name="fgtPwd">Forgot Password?</a></br></br></br>
				<input class="inp-button" type="submit" value="Sign in" />
				<a href="register.php" class="inp-button">New user? Sign up</a>
			</form>
		</div>
	<!--</div>-->

<?php #include "footer.php" ?>

</body>
</html>
