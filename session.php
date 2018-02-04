<?php
try 	{
	$sql_fetch_user_details="select *
							from users 
							where user_id='".$userid."'";
	
	$stmt=$conn->prepare($sql_fetch_user_details);
	$stmt->execute();
	$row=$stmt->fetch();
	$name=addslashes($row['disp_name']);
	$user=addslashes($row['user_id']);
	$mail=$row['email_addr'];
	$ph_num=$row['ph_num'];
	$dob=$row['dob'];
	$desc=addslashes($row['description']);
	$shrt_bio=addslashes($row['shrt_bio']);
	$location=addslashes($row['location']);
	$upvotes=$row['up_votes'];
	$downvotes=$row['down_votes'];
	$pro_img=$row['pro_img_url'];
	
	$sql_get_follow_dtls="select count(1) count1 from followers where following_user_id='".$userid."'";
	foreach($conn->query($sql_get_follow_dtls) as $res_fl1)
		$follow_cnt1 = $res_fl1['count1'];
	$sql_get_following_dtls="select count(1) count2 from followers where user_id='".$userid."'";
	foreach($conn->query($sql_get_following_dtls) as $res_fl2)
		$follow_cnt2 = $res_fl2['count2'];
		
	$tags_list=array();
	$tags_str="";
	try	{
		$sql_check_interests = "select b.tag_name 
								from tags b 
								inner join user_tags a 
								on b.tag_id = a.tag_id
								where a.user_id = '".$userid."'";
		$stmt_check_interests = $conn->prepare($sql_check_interests);
		$stmt_check_interests->execute();
		
		if($stmt_check_interests->rowCount() > 0)	{
			while($row_interests = $stmt_check_interests->fetch())	{
				array_push($tags_list,$row_interests['tag_name']);
			}
			$tags_str=implode($tags_list,", ");
		}
	}
	catch(PDOException $e)	{
		
	}
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
$_SESSION['shrt_bio']=$shrt_bio;
$_SESSION['location']=$location;
$_SESSION['ph_num']=$ph_num;
$_SESSION['dob']=$dob;
$_SESSION["up_votes"]=$upvotes;
$_SESSION["down_votes"]=$downvotes;
$_SESSION["flw_1"]=$follow_cnt1;
$_SESSION["flw_2"]=$follow_cnt2;
$_SESSION["interest"]=$tags_str;
$_SESSION["interest_list"]=$tags_list;
?>
