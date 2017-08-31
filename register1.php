<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BioForum : User registration</title>
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
$regmsg=$name=$fullname=$pwd=$repwd=$age=$email=$phone=$gender=$countries=$abtme=$propic="";					#Variables to store input values.
$userErr=$nameErr=$pwdErr=$repwdErr=$emailErr=$phoneErr=$ageErr=$countryErr=$ageErr=$genErr=$propicErr="";		#Variables to return error messages.
$checked=true;	#Boolean variable to check if all fields are correct or not.

if($_SERVER["REQUEST_METHOD"]=="POST")	{
	
	#Check for proper name - empty/invalid characters
	if(!empty($_POST["name"]))	{
		$fullname=processData($_POST["name"]);
		if (!preg_match("/^[a-zA-Z ]*$/",$fullname)) {
			$nameErr = "Only letters and white space allowed"; 
			$checked=false;
			$name="";
		}
	}
	else	{
		$nameErr="Enter your name";
		$checked=false;
	}		
	
	#Validate username - empty field
	if(empty($_POST["user"]))	{
		$userErr="Enter Username";
		$checked=false;
	}
	else
		$name=processData($_POST["user"]);
	
	#validate password - empty field
	if(empty($_POST["pwd"]))	{
		$pwdErr="Enter your password";
		$checked=false;
	}
	else
		$pwd=processData($_POST["pwd"]);
	
	#validate password - empty field
	if(empty($_POST["repwd"]))	{
		$repwdErr="Enter password again";
		$checked=false;
	}
	else
		$repwd=processData($_POST["repwd"]);
	
	#Check if two password fields are same.
	if(checkPwdEquality($pwd,$repwd) != 0)	{
		$repwdErr="Passwords do not match!";
		$checked=false;
	}
	
	#validate email address
	if(!empty($_POST["mail"]))	{
		$email=processData($_POST["mail"]);
		if(!validate_email($email))	{
			$emailErr="Invalid Email - Try again";
			$checked=false;
			$email="";
		}
	}
	else	{
		$emailErr="Enter your EmailId";
		$checked=false;
	}
		
	
	#check for proper mobile number
	if(!empty($_POST["phone"]))	{
		$phone=processData($_POST["phone"]);
		if(filter_var($phone, FILTER_VALIDATE_INT) === false)	{
			$phone_len = strlen((string)$phone);
			if($phone_len < 10)	{
				$phoneErr="Invalid mobile number";
				$checked=false;
				$phone="";
			}
		}
		else	{
			$phoneErr="Enter proper mobile number";
			$phone="";
			$checked=false;
		}
	}
	else	{
		$phoneErr="Enter your mobile number";
		$checked=false;
	}
	
	#check for empty field
	if(empty($_POST["age"]))	{
		$ageErr="Enter your age";
		$checked=false;
	}
	else
		$age=processData($_POST["age"]);
	
	#check for empty field.
	if(empty($_POST["gender"]))	{
		$genErr="Choose your gender";
		$checked=false;
	}
	else
		$gender=processData($_POST["name"]);
	
	#check for empty field.
	if(empty($_POST["countries"]))	{
		$countryErr="What's your location?";
		$checked=false;
	}
	else
		$countries=processData($_POST["countries"]);

	$abtme=processData($_POST["abtme"]);
	
	#If everything is fine - Add the user.
	if($checked==true)	{
		try	{
			$sql="insert into users
			(user_id,disp_name,encrypt_pwd,email_addr,ph_num,age,location,description,pro_img_url,status,up_votes,down_votes)
			values
			('".$name."','".$fullname."','".md5($pwd)."','".$email."',".$phone.",".$age.",'".$countries."','".$abtme."','uploads/male.jpg','A',0,0)";
			
			$conn->exec($sql);
			$regmsg="Registration successfull";
			include "session.php";	#Starting user session
			echo '<script type="text/javascript">window.location="upload.php";</script>';		#On successful registration, redirect to profile pic upload
		}
		catch(PDOException $e)	{
			$regmsg="Some error occured".$e->getMessage();
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

#Password equality check
function checkPwdEquality($pass1,$pass2)	{
	return strcmp($pass1,$pass2);
}

function validate_email($email)	{
	if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		return false;
	return true;
}


 ?>
<!--<h2>User Registration</h2>-->
</br>
<div class="container">
	<div class="row">
		<div class="col-sm-12">
			Enter full name: 
			<input class="inp-txt" id="name" type="text" name="" value="<?php echo $fullname; ?>" onfocusout="validateData(this.value,'name-error')" />
			<span id="name-error" class="col-3-data">error</span></br></br>
			Choose User ID: 
			<input class="inp-txt" type="text" id="user" name="" value="<?php echo $name; ?>" onfocusout="validateUser(this.value)" />
			<span id="user-error" class="col-3-data"></span>
		</div>
	</div>
</div>

<?php #include "footer.php"; ?>
</body>
</html>