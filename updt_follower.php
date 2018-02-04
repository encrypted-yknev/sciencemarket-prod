<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";

if(isset($_REQUEST['user_id']))
	$user_id = htmlspecialchars(stripslashes(trim($_REQUEST['user_id'])));
if(isset($_REQUEST['flag']))
	$flag = htmlspecialchars(stripslashes(trim($_REQUEST['flag'])));

if($flag==0)	{
	
	try	{
		$sql_check_follow="select count(1) as check_1 from followers where user_id='".$_SESSION['user']."' and following_user_id='".$user_id."'";
		foreach($conn->query($sql_check_follow) as $row_count)
			$count1 = $row_count['check_1'];
		if($count1 > 0)
			echo "<span style='color:#3c763d;'>You are already following <strong>".$user_id."</strong></span>";
		else	{
			$sql_update_follower="insert into followers (user_id,following_user_id) values ('".$_SESSION['user']."','".$user_id."')";
			$stmt=$conn->prepare($sql_update_follower);
			$stmt->execute();
			if($stmt->rowCount() > 0)	{
				echo "<span style='color:#3c763d;'>You are now following <strong>".$user_id."</strong></span>";
			}
			else	{
				echo "<span style='color:#a94442;'>Some error occurred</span>";
			}
		}
	}
	catch(PDOException $e)	{
		echo "<span style='color:#a94442;'>Internal server error</span>";
	}
}
else if($flag==1)	{
	try	{
		$sql_check_follow="select count(1) as check_1 from followers where user_id='".$_SESSION['user']."' and following_user_id='".$user_id."'";
		foreach($conn->query($sql_check_follow) as $row_count)
			$count1 = $row_count['check_1'];
		if($count1 > 0)		{
			$sql_update_follower="delete from followers where user_id ='".$_SESSION['user']."' and following_user_id = '".$user_id."'";
			$stmt=$conn->prepare($sql_update_follower);
			$stmt->execute();
			if($stmt->rowCount() > 0)	{
				echo "<span style='color:#3c763d;'>You have unfollowed <strong>".$user_id."</strong></span>";
			}
			else	{
				echo "<span style='color:#a94442;'>Some error occurred</span>";
			}
		}
		else	
			echo "<span style='color:#a94442;'>You have already unfollowed <strong>".$user_id."</strong></span>";
	}
	catch(PDOException $e)	{
		echo "<span style='color:#a94442;'>Internal server error</span>";
	}
}

