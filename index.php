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
<title>Science Market - Home | Where people discuss and collaborate with experts</title>
<meta name="description" content="Science Market is an online market place to connect with peers, people, groups or expert. Discuss topics in question answer forum, connect with experts under expert connect, collaborate with people and provide favours." >
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="/styles/login.css" >
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/login.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">

</head>
<body>

<?php
include "connectDb.php";
$message="<div class='msg-default'>Login Portal</div>";
$pwddb="";
$userid="";
$nameError="*";
$nameSuc="";
$pwdError="*";
$checked=$success=true;
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	#check for empty field
	if(empty($_POST["userid"]))	{
		$message="<div class='alert alert-danger login-message'>Enter mandatory fields</div>";
		$checked=false;
	}
	else	{
		#check for empty field.
		$userid=processData($_POST["userid"]);
		if(empty($_POST["pwd"]))	{
			$message="<div class='alert alert-danger login-message'>Enter mandatory fields</div>";
			$checked=false;
		}
		else	{
			$pwd=processData($_POST["pwd"]);
		}
	}
	
	if($checked==true)	{
		try	{
			$sql="select status,encrypt_pwd from users where user_id='".$userid."'";
			$stmt=$conn->prepare($sql);
			$stmt->execute();
			
			if($stmt->rowCount() > 0)	{
				$row=$stmt->fetch();
				$pwddb=$row['encrypt_pwd'];
				$status=$row['status'];
				
				#check is user entered the correct password.
				if(md5($pwd)!=$pwddb)	{
					$message="<div class='alert alert-danger login-message'>Invalid credentials</div>";
					$success=false;
				}
				else	{
					if(strtoupper($status) != 'A')	{
						$message = "<div class='alert alert-warning login-message'>Account is de-activated</div>";
						$success=false;
					}
					#Redirect to dashboard welcome page for correct credentials.
					else	{
						include "session.php";	#start user session
						header("location:dashboard.php");
					}
				}
			}
			else	{
				$message="<div class='alert alert-warning login-message'>Looks like you are a new user. Please register</div>";
			}
		}
		catch(PDOException $e)	{
			$message= "<div class='alert alert-danger login-message'>Internal server error</div>";
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
			<div id="login-logo">
				<img id="" src="img/logo4.svg" width="70" height="70"/>
				<img id="" src="img/logo.svg" width="250" height="70"/>
			</div></br>	
			
			<?php echo $message; ?>
			<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
				<div class="form-group form-section">
					<label for="name-area" class="form-labels">Username</label>
					<input class="form-control input-class" id="name-area" type="text" name="userid" placeholder="Enter login ID" value="<?php echo $userid;?>" />
				</div>
				<div class="form-group form-section">
					<label for="pwd-area" class="form-labels">Password</label>
					<input class="form-control input-class" id="pwd-area" type="password" name="pwd" placeholder="Enter password" />
				</div>	
				
			<!--	<div class="g-signin2" data-onsuccess="onSignIn"></div></br> -->
				
				<div class="form-row-2">
					<table>
					<tr><td>
						<span class="checkbox">
							<label><input class="" type="checkbox" value="" name="remMe">Remember me</label>
						</span>
						</td>
						<td>
						<a id="fp-link" href="frgt_pwd.php" target="_blank" name="fgtPwd">Forgot Password?</a>
						</td>
					</tr>
					</table>
				</div></br>
				<div class="form-row-3">
					<input class="btn btn-primary inp-button" type="submit" value="Sign in" />
					<a href="register.php" class="btn btn-primary inp-button">New user? Sign up</a>
				</div>
			</form>
		</div>
	<!--</div>-->

</body>
</html>
