<!DOCTYPE html>
<html lang="en">
<head>
<title>BioForum - Password reset</title>
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
$message="";
$email="";
$checked=true;

if($_SERVER["REQUEST_METHOD"]=="POST")	{
	if(!empty($_POST["mail"]))	{
		$email=processData($_POST["mail"]);
		if(!validate_email($email))	{
			$message="Invalid Email - Try again";
			$checked=false;
			$email="";
		}
		else	{
			$sql_fetch_user="select count(1) as cnt_user from users where email_addr = '".$email."'";
			$stmt=$conn->prepare($sql_fetch_user);
			$stmt->execute();
			$result=$stmt->fetch();
			$count=$result['cnt_user'];
			
			if($count == 0)
				$message="Uh oh! No such user exists";
			else if($count > 1)
				$message="multiple user with same e-mail";
			else if($count == 1)	{
				
			}
		}
	}
	else	{
		$message="Enter your EmailId";
		$checked=false;
	}
}
	
#Function to trim extra spaces/backslashes and avoiding cross-scripting
function processData($text)	{
	$text=trim($text);
	$text=stripslashes($text);
	$text=htmlspecialchars($text);
	return $text;
}
function validate_email($email)	{
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		return false;
	return true;
}
?>
<div id="bg-window"></div>
	<!--<div class="container"> -->
<div id="main-container-2">
	<h2>Password reset</h2>
	<span id="err1"><?php echo $message; ?></span></br></br>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<div class="form-section">
			Registered e-mail : <input class="input-class" id="mail-area" type="text" name="mail" value="<?php echo $email;?>" /></span></br></br></br>
			<input class="inp-button" type="submit" value="Sign in" />
			<a href="register.php" class="inp-button">New user? Sign up</a>
		</div>
	</form>
</div>
	<!--</div>-->

<?php #include "footer.php" ?>

</body>
</html>
