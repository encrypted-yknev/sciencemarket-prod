<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";

$request_type=htmlspecialchars(stripslashes(trim($_REQUEST["request_type"])));
$user_dtls=array();
$user_dtls["request_type"]=$request_type;
$user_dtls["user_id"]=$_SESSION['user'];

if($request_type == 1)	{
	$user=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["user"]))));
	if(strlen(trim($user))==0)
		$user=$_SESSION['user'];
	$name=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["name"]))));
	if(strlen(trim($name))==0)
		$name=$_SESSION['name'];
	$mail=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["mail"]))));
	if(strlen(trim($mail))==0)
		$mail=$_SESSION['mail'];
	$mob=htmlspecialchars(stripslashes(trim($_REQUEST["mob"])));
	$location=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["place"]))));
	$dob=trim($_REQUEST["dob"]);
	$desc=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["desc"]))));
	$shrt_bio=addslashes(htmlspecialchars(stripslashes(trim($_REQUEST["shrt_bio"]))));
	if(strlen(trim($desc))==0)
		$desc=$_SESSION['desc'];
	if(strlen(trim($shrt_bio))==0)
		$shrt_bio=$_SESSION['shrt_bio'];
	
	$user_dtls["request_dtls"]["user"]=$user;
	$user_dtls["request_dtls"]["name"]=$name;
	$user_dtls["request_dtls"]["mail"]=$mail;
	$user_dtls["request_dtls"]["mob"]=$mob;
	$user_dtls["request_dtls"]["location"]=$location;
	$user_dtls["request_dtls"]["desc"]=$desc;
	$user_dtls["request_dtls"]["shrt_bio"]=$shrt_bio;
	$user_dtls["request_dtls"]["dob"]=$dob;
	
}
else if($request_type == 2)	{
	$interest_list=htmlspecialchars(trim($_REQUEST['int-val']));
	$user_dtls["request_dtls"]["interest_list"]=$interest_list;
}
else if($request_type == 3)	{
	$old_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['old_pwd'])));
	$new_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['new_pwd'])));
	$conf_pwd=htmlspecialchars(stripslashes(trim($_REQUEST['conf_pwd'])));
	
	$user_dtls["request_dtls"]["old_pwd"]=md5($old_pwd);
	$user_dtls["request_dtls"]["new_pwd"]=md5($new_pwd);
	$user_dtls["request_dtls"]["confirm_pwd"]=md5($conf_pwd);
	
}
else if($request_type == 4)	{
	$pwd_deactvt=htmlspecialchars(trim($_REQUEST['pwd']));
	$user_dtls["request_dtls"]["encrypt_pwd"]=md5($pwd_deactvt);
}

$user_dtls_json=json_encode($user_dtls);

try		{
	$sql_call_sp_user_updt="call user_update('".$_SESSION['user']."',".$request_type.",'".$user_dtls_json."',@err_cd,@err_desc)";
				
	$stmt_call_sp_user_updt=$conn->prepare($sql_call_sp_user_updt);
	$stmt_call_sp_user_updt->execute();
	
	$row_sp = $conn->query("select @err_cd as error_code,@err_desc as error_desc")->fetch();
	
	$error_code=$row_sp['error_code'];
	$error_desc=$row_sp['error_desc'];
	if(!strcmp($error_code,'00000'))	{
		$userid=$_SESSION['user'];
		include "session.php";
		if($request_type == 4)
			echo "<div class='alert alert-warning msg-profile'>Account de-activated</div>";
		else
			echo "<div class='alert alert-success msg-profile'>Details updated successfully</div>";
	}
	else	{
		echo "<div class='alert alert-danger msg-profile'>".$error_desc."</div>";
	}
}
catch(PDOException $e)	{
	echo "Error updating user details";
}
