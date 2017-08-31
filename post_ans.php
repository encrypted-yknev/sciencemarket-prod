<?php
session_start();

include "connectDb.php";

$ans_desc=$_REQUEST["ans"];
$qid=$_REQUEST["qid"];
$posted_by=$_REQUEST["postedBy"];

if(!empty($ans_desc))	{
	$user_ans=htmlspecialchars(trim($ans_desc),ENT_QUOTES);
	try	{
		$sql_get_qstn_titl = "select qstn_titl from questions where qstn_id = ".$qid;
		foreach($conn->query($sql_get_qstn_titl) as $row_titl)
			$qstn_title = $row_titl['qstn_titl'];
		$sql_ans_ins="insert into answers (ans_desc,qstn_id,posted_by) 
						values ('".$user_ans."',".$qid.",'".$_SESSION["user"]."')";
		$conn->exec($sql_ans_ins);
		
		if($posted_by != $_SESSION['user'])	{
			$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."'  class='list-group-item'>".$_SESSION['user']." posted an answer to your question on <strong>".$qstn_title."</strong></a>");
			try		{
				$sql_push_notifications = "insert into notifications(notify_text,user_id,view_flag)
											values ('".$notify_text."','".$posted_by."',0)";
				$stmt_push_notify = $conn->prepare($sql_push_notifications);
				$stmt_push_notify->execute();
				if($stmt_push_notify->rowCount() > 0)
					echo "<strong>Thank you! Your answer has been posted.</strong></br>";
				else
					echo "Some error occurred. We are trying to fix the issues";
			}
			catch(PDOException $e)	{
				
			}
		}
		/* delete old notifications for user who posted an answer  */
		
		$sql_remove_old_notify = "select count(1) as count from notifications where user_id = '".$posted_by."'";
		$stmt_remove_old_notify = $conn->prepare($sql_remove_old_notify);
		$stmt_remove_old_notify->execute();
		$result_1 = $stmt_remove_old_notify->fetch();
		$count_1 = $result_1['count'];
		
		if($count_1 > 100)	{
			$net_count = $count_1 - 100;
			$sql_final_remove = "delete from notifications order by created_ts asc limit ".$net_count;
			$stmt_final_remove = $conn->prepare($sql_final_remove);
			$stmt_final_remove->execute();
		}	
		/* end of delete */
		$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."' class='list-group-item'><strong>".$_SESSION['user']."</strong> also posted an answer on <strong>".$posted_by."'s</strong> question on <strong>".$qstn_title."</strong></a>");
		$sql_push_notifications_1 = "insert into notifications(notify_text,user_id,view_flag)
								   select distinct '".$notify_text."',posted_by,0 from answers where qstn_id = ".$qid." and posted_by <> '".$_SESSION['user']."'";
		$stmt_push_notifications_1 = $conn->prepare($sql_push_notifications_1);
		$stmt_push_notifications_1->execute();
	}
	catch(PDOException $e)	{
		echo "Error posting answer";
	}
}
else 	{
	echo "Please write some answer";
}
?>