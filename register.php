<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market : Get started. Register here.</title>
<link rel="stylesheet" type="text/css" href="styles/register.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/register.js"></script>
</head>
<body>
<?php #include "header.php";
include "connectDb.php";		#connect to mysql database.

$name=$userid=$pwd=$repwd=$mail="";					#Variables to store input values.
$message="";
if($_SERVER["REQUEST_METHOD"]=="POST")	{
	
	#Check for proper name - empty/invalid characters
	$name=htmlspecialchars(stripslashes(trim($_POST['name'])));
	$userid=htmlspecialchars(stripslashes(trim($_POST['user'])));
	$pwd=htmlspecialchars(stripslashes(trim($_POST['pwd'])));
	$hashed_pwd=md5($pwd);
	$mail=htmlspecialchars(stripslashes(trim($_POST['mail'])));
	
	if(!empty($name) && !empty($userid) && !empty($hashed_pwd) && !empty($mail))	{
	#If everything is fine - Add the user.
		try	{
			$sql="insert into users
			(user_id,disp_name,encrypt_pwd,email_addr,status)
			values
			('".$userid."','".$name."','".$hashed_pwd."','".$mail."','A')";
			
			$conn->exec($sql);
			$message = "Registration successful";
			session_start();
			include "session.php";	#Starting user session
			header("location:user_preference.php");		#On successful registration, redirect to dashboard
		}
		catch(PDOException $e)	{
			$message = "Registration failed ".$e->getMessage();
		}
	}
	else
		$message = "Uh oh!! All fields are mandatory";
}


 ?>
<!--<h2>User Registration</h2>-->
</br>
<div class="container">
	<div id="reg-container">
			<div class="row">
				<div id="logo-section" class="col-sm-4">
					<img src="img/logo4.svg" width="55" height="55"/>
					<img src="img/logo.svg" width="140" height="60"/>
				</div>
				<div class="col-sm-8">
					<span id="page-caption"><em>An online portal to discuss stuffs among peers, connect with experts and collaborate with people. Register now to get started</em></span>
				</div>
			</div></br>
			<div id="main-err-message"><?php echo $message; ?></div></br>
			<form class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" name="user-form" method="post">
				
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="name">Enter full name</label>
					<div class="col-sm-4">
						<input class="form-control" id="name" type="text" name="name" onfocus="$('#msg-1').show();" onblur="$('#msg-1').hide();" value="<?php echo $name; ?>" 
							onfocusout="validateData(this.value,'name-error')" /> 
						<div class="msg-info" id="msg-1">Enter valid name. Should contain only spaces and alphabets</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="name-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="user">Choose UserID</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="user" name="user" onfocus="$('#msg-2').show();" onblur="$('#msg-2').hide();" value="<?php echo $userid; ?>" 
							 onfocusout="validateUser(this.value)" />
						<div class="msg-info" id="msg-2">Username must be unique</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="user-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="pwd">Choose password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="pwd" name="pwd" onfocus="$('#msg-3').show();" onblur="$('#msg-3').hide();"
							 onfocusout="validatePassFld(this.value,'pwd-error')" />
						<div class="msg-info" id="msg-3">Password should contain minimum of 8 characters</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="pwd-error" class="col-3-data"></span>
					</div>
				</div>			
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="repwd">Re-enter password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="repwd" onfocus="$('#msg-4').show();" onblur="$('#msg-4').hide();"
								onfocusout="validateRePassFld(document.getElementById('pwd').value,this.value,'repass-error')" />
						<div class="msg-info" id="msg-4">Re-enter same password as above</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="repass-error" class="col-3-data"></span>
					</div>
				</div>			
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="mail">Email</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="mail" name="mail" onfocus="$('#msg-5').show();" onblur="$('#msg-5').hide();" value="<?php echo $mail; ?>" />
						<div class="msg-info" id="msg-5">Enter valid email of format abc@xyz.pqr</div>
					</div>
					<div class="col-sm-4 message-section">
						<span id="user-error" class="col-3-data"></span>
					</div>
				</div></br>
				<div id="button-section">
					<input id="reg-button" class="btn btn-default nav-button" type="submit" value="Register" />
					<input id="res-button" class="btn btn-default nav-button" type="reset" value="Reset" />
					<a href="index.php" id="home-button" class="btn btn-primary nav-button">Home</a>
				</div>
			</form>
	</div>	
			
	
	
				<!--
				<tr>
					<td class="col-1">Mobile: </td>
					<td class="col-2">
						<input class="inp-txt" type="text" id="phone" name="mob" value="<?php #echo $phone; ?>" onfocus="showTip(6)" onfocusout="validateMob(this.value,'mob-error')"/>
					</td>
					<td>
						<span class="col-3-data" id="mob-error"></span>
					</td>
				</tr>
				<tr>
					<td class="col-1">How old are you? </td>
					<td class="col-2">
						<input class="inp-txt" type="text" id="age" name="age" value="<?php #echo $age; ?>">
					</td>
					<td>
						<span class="col-3-data"></span>
					</td>
				</tr>
				<tr>
					<td class="col-1">Gender: </td>
					<td class="col-2">
						<input class="inp-txt gender-sec" type="radio" name="gender" value="male" >Male
						<input class="inp-txt gender-sec" type="radio" name="gender" value="female" >Female
					</td>
					<td>
						<span class="col-3-data"></span>
					</td>
				</tr>
				<tr>
					<td class="col-1">Tell us your Location: </td>
					<td class="col-2">
						<select class="inp-txt" id="countries" name="location" >
							<option>USA</option>
							<option>India</option>
							<option>Australia</option>
							<option>Canada</option>
							<option>Japan</option>
							<option>China</option>
							<option>New Zealand</option>
							<option>South Africa</option>
						</select>
					</td>
					<td>
						<span class="col-3-data"></span>
					</td>
						
				</tr>
				<tr><td class="col-1">Tell something about yourself: </td>
				
			
			<td class="col-2">
				<textarea class="form-control" rows="2" id="desc-box" name="desc" placeholder="Your description" onfocus="showTip(7)"></textarea>
			</td>
			
			<tr>
			<td>
				<span class="col-3-data"></span>
			</td>
			</tr>
			
			</table>
			-->
			
		<!--	<button id="reg-button" onclick="postData(document.getElementById('name').value,
																	   document.getElementById('user').value,
																	   document.getElementById('pwd').value,
																	   document.getElementById('mail').value,
																	   document.getElementById('phone').value,
																	   document.getElementById('age').value,
																	   document.getElementById('countries').value,
																	   document.getElementById('desc-box').value)">Register</button>-->
		
	</div>
<?php #include "footer.php"; ?>
</body>
</html>
