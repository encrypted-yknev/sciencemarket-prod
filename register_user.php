<?php 
include "connectDb.php";		#connect to mysql database.
$regmsg=$name=$fullname=$pwd=$repwd=$age=$email=$phone=$gender=$countries=$abtme=$propic="";					#Variables to store input values.
$userErr=$nameErr=$pwdErr=$repwdErr=$emailErr=$phoneErr=$ageErr=$countryErr=$ageErr=$genErr=$propicErr="";		#Variables to return error messages.
$checked=true;	#Boolean variable to check if all fields are correct or not.

$name=htmlspecialchars(stripslashes(trim($_REQUEST['name'])));
$user=htmlspecialchars(stripslashes(trim($_REQUEST['user'])));
$pwd=htmlspecialchars(stripslashes(trim($_REQUEST['pwd'])));
$hashed_pwd=md5($pwd);
$mail=htmlspecialchars(stripslashes(trim($_REQUEST['mail'])));
$mob=htmlspecialchars(stripslashes(trim($_REQUEST['mob'])));
$age=htmlspecialchars(stripslashes(trim($_REQUEST['age'])));
$location=htmlspecialchars(stripslashes(trim($_REQUEST['location'])));
$desc=htmlspecialchars(stripslashes(trim($_REQUEST['desc'])));
	
	if(!empty($name) && !empty($user) && !empty($hashed_pwd) && !empty($mail) && !empty($mob) && !empty($age) && !empty($location))	{
	#If everything is fine - Add the user.
		try	{
			$sql="insert into users
			(user_id,disp_name,encrypt_pwd,email_addr,ph_num,age,location,description,pro_img_url,status,up_votes,down_votes)
			values
			('".$user."','".$name."','".$hashed_pwd."','".$mail."',".$mob.",".$age.",'".$location."','".$desc."','uploads/male.jpg','A',0,0)";
			
			$conn->exec($sql);
			echo "Registration successful";
			include "session.php";	#Starting user session
			#header("location:dashboard.php");		#On successful registration, redirect to profile pic upload
		}
		catch(PDOException $e)	{
			echo "Registration failed ";
		}
	}
	else
		echo "Enter the required fields";
	