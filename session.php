<?php


try 	{
	$sql_fetch_user_details="select user_id,
									disp_name,
									email_addr,
									ph_num,
									age,
									location,
									description,
									pro_img_url,
									up_votes,
									down_votes,
									created_ts,
									last_updt_ts 
							from users 
							where user_id='".$userid."'";
	
	$stmt=$conn->prepare($sql_fetch_user_details);
	$stmt->execute();
	$row=$stmt->fetch();
	$name=$row['disp_name'];
	$user=$row['user_id'];
	$mail=$row['email_addr'];
	$desc=$row['description'];
	$location=$row['location'];
	$upvotes=$row['up_votes'];
	$downvotes=$row['down_votes'];
	$pro_img=$row['pro_img_url'];
}

catch(PDOException $e)	{
	echo $e->getMessage();
}

$_SESSION["logged_in"]=true;
$_SESSION["user"]=$user;
$_SESSION["pro_img"]=$pro_img;
$_SESSION["name"]=$name;
$_SESSION['mail']=$mail;
$_SESSION['desc']=$desc;
$_SESSION["up_votes"]=$upvotes;
$_SESSION["down_votes"]=$downvotes;
?>
