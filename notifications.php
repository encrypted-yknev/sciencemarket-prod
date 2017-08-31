<?php
session_start();
include "connectDb.php";

if(isset($_REQUEST['user']))
	$user_id = $_REQUEST['user'];
if(isset($_REQUEST['notify_text']))
	$notify_text = $_REQUEST['notify_text'];
if(isset($_REQUEST['notify_typ']))
	$notify_typ = $_REQUEST['notify_typ'];

if(strcmp($notify_typ,"DISPLAY") == 0)	{
	try		{
		$sql_get_notifications = "select notify_id,notify_text from notifications where view_flag = 0 and user_id = '".$_SESSION['user']."' order by created_ts desc";
		$stmt_get_notify = $conn->prepare($sql_get_notifications);
		$stmt_get_notify->execute();
		
		if($stmt_get_notify->rowCount() > 0)	{
			echo '<div class="list-group">';
			while($row_notify = $stmt_get_notify->fetch())	{
				$user_notify_text = $row_notify['notify_text'];
				$user_notify_id = $row_notify['notify_id'];
				echo $user_notify_text;
			}
			echo "</div>";
		}
		else
			echo "You don't have any new notifications";
			
	}
	catch(PDOException $e)	{
		echo "Some error occured in fetching notifications. Please try again after some time";
	}
}
else if(strcmp($notify_typ,"PUSH") == 0)	{
	try		{
		$sql_push_notifications = "insert into notifications(notify_text,user_id,view_flag)
									values ('".$notify_text."','".$user_id."',0)";
		$stmt_push_notify = $conn->prepare($sql_push_notifications);
		$stmt_push_notify->execute();
	}
	catch(PDOException $e)	{
		
	}
}
else if(strcmp($notify_typ,"DELETE") == 0)	{
	try		{
		$sql_del_notifications = "update notifications set view_flag = 1 where ";
		$stmt_push_notify = $conn->prepare($sql_del_notifications);
		$stmt_push_notify->execute();
	}
	catch(PDOException $e)	{
		
	}
}



?>