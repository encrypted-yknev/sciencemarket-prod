<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market : Get started. Register here.</title>
<link rel="stylesheet" type="text/css" href="styles/register.css">
<link rel="stylesheet" type="text/css" href="styles/header.css">
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
			(user_id,disp_name,encrypt_pwd,email_addr,pro_img_url,status)
			values
			('".$userid."','".$name."','".$hashed_pwd."','".$mail."','uploads/male.jpg','A')";
			
			$conn->exec($sql);
			$message = "Registration successful";
			session_start();
			include "session.php";	#Starting user session
			header("location:dashboard.php");		#On successful registration, redirect to dashboard
		}
		catch(PDOException $e)	{
			$message = "Registration failed ";
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
				<div class="col-sm-4">
					<img src="img/logo.jpg" width="200" height="50"/>
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
						<input class="form-control" id="name" type="text" name="name" value="<?php echo $name; ?>" 
							onfocus="showTip(1)" onfocusout="validateData(this.value,'name-error')" />
					</div>
					<div class="col-sm-4 message-section">
						<span id="name-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="user">Choose UserID</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="user" name="user" value="<?php echo $userid; ?>" 
								onfocus="showTip(2)" onfocusout="validateUser(this.value)" />
					</div>
					<div class="col-sm-4 message-section">
						<span id="user-error" class="col-3-data"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="pwd">Choose password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="pwd" name="pwd" 
								onfocus="showTip(3)" onfocusout="validatePassFld(this.value,'pwd-error')" />
					</div>
					<div class="col-sm-4 message-section">
						<span id="pwd-error" class="col-3-data"></span>
					</div>
				</div>			
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="repwd">Re-enter password</label>
					<div class="col-sm-4">
						<input class="form-control" type="password" id="repwd" 
								onfocus="showTip(4)" onfocusout="validateRePassFld(document.getElementById('pwd').value,this.value,'repass-error')" />
					</div>
					<div class="col-sm-4 message-section">
						<span id="repass-error" class="col-3-data"></span>
					</div>
				</div>			
				<div class="form-group">
					<label class="control-label col-sm-4 form-label" for="mail">Email</label>
					<div class="col-sm-4">
						<input class="form-control" type="text" id="mail" name="mail" value="<?php echo $mail; ?>" onfocus="showTip(5)" />
					</div>
					<div class="col-sm-4 message-section">
						<span id="user-error" class="col-3-data"></span>
					</div>
				</div></br>
				<div id="button-section">
					<input id="reg-button" class="btn btn-default" type="submit" value="Register" />
					<input id="res-button" class="btn btn-default" type="reset" value="Reset" />
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