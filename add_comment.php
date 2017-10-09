<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";
function convert_utc_to_local($utc_timestamp)	{
	$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
	$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
	$date_final = $date_utc->format('Y-m-d H:i:s');
	return $date_final;
}
function get_user_date($time)	{
	$date = substr($time,8,2);
	$month = substr($time,5,2);
	$year = substr($time,0,4);
	$mth_str="";
	switch($month)	{
		case "01": $mth_str="Jan";
			break;
		case "02": $mth_str="Feb";
			break;
		case "03": $mth_str="Mar";
			break;
		case "04": $mth_str="Apr";
			break;
		case "05": $mth_str="May";
			break;
		case "06": $mth_str="Jun";
			break;
		case "07": $mth_str="Jul";
			break;
		case "08": $mth_str="Aug";
			break;
		case "09": $mth_str="Sep";
			break;
		case "10": $mth_str="Oct";
			break;
		case "11": $mth_str="Nov";
			break;
		case "12": $mth_str="Dec";
			break;
		default : $mth_str = "";
		break;
	}
	/* if(substr($date,1,1) == '1' and $date != "11")
		$post_date_str = "ST";
	else if(substr($date,1,1) == '2' and $date != "12")
		$post_date_str = "ND";
	else if(substr($date,1,1) == '3' and $date != "13")
		$post_date_str = "RD";
	else 
		$post_date_str = "TH"; */
	
	if($date == date('d') and substr($time,11,5) == date("H:i"))
		return 'few seconds ago';
	if($date == date('d'))
		return 'Today '.substr($time,11,5);
	
	return $mth_str.' '.$date.', '.$year;
	
}
$text_val=addslashes($_REQUEST['text']);
$ans_id=$_REQUEST['ansid'];
$posted_by = $_REQUEST['posted_by'];
$qid = $_REQUEST['qid'];
$q_post_by = $_REQUEST['q_posted_by'];

try	{
	$sql_get_qstn_title = "select qstn_titl from questions where qstn_id = ".$qid;
	foreach($conn->query($sql_get_qstn_title) as $result_titl)
		$qstn_titl=$result_titl['qstn_titl'];
}
catch(PDOException $e)	{
	
}
try	{
	$sql_add_comment="insert into comments(comment_desc,ans_id,posted_by) values('".$text_val."',".$ans_id.",'".$_SESSION['user']."')";
	$stmt=$conn->prepare($sql_add_comment);
	$stmt->execute();
	
	$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."#user-answer-".$ans_id."' class='list-group-item'><strong>".$_SESSION['user']."</strong> commented on your answer</a>");
	$notify_text_qstn = addslashes("<a href = 'qstn_ans.php?qid=".$qid."#user-answer-".$ans_id."' class='list-group-item'><strong>".$_SESSION['user']."</strong> commented on <strong>".$posted_by."'s</strong> answer for your question on <strong>".$qstn_titl."</strong></a>");
	
	/* Build JSON for notification config for users who posted that question 	*/
		$myObj=array();
		$myObj["post_type"] = "C";
		$myObj["user_id"] = $_SESSION['user'];
		$myObj["ans_config"]["ans_id"] = $ans_id;
		$myObj["ans_config"]["ans_posted_by"] = $posted_by;
		$myObj["qstn_config"]["qstn_id"] = $qid;
		$myObj["qstn_config"]["qstn_posted_by"] = $q_post_by;
		$myJSON = json_encode($myObj);
	/* end of build*/
	
	try		{
		/* push notification for user who posted the answer and question */
		if($posted_by != $_SESSION['user'])	{
			$sql_push_notifications = "insert into notifications(notify_confg,user_id,view_flag)
									   values('".$myJSON."','".$posted_by."',0)";
			$stmt_push_notifications = $conn->prepare($sql_push_notifications);
			$stmt_push_notifications->execute();
		}
		if($q_post_by != $_SESSION['user'] and $q_post_by != $posted_by)	{
			$sql_push_notifications_q = "insert into notifications(notify_confg,user_id,view_flag)
									   values('".$myJSON."','".$q_post_by."',0)";
			$stmt_push_notifications_q = $conn->prepare($sql_push_notifications_q);
			$stmt_push_notifications_q->execute();
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
		
		$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."#user-answer-".$ans_id."' class='list-group-item'><strong>".$_SESSION['user']."</strong> also commented on <strong>".$posted_by."'s</strong> answer</a>");
		
		$sql_push_notifications_1 = "insert into notifications(notify_confg,user_id,view_flag)
								   select distinct '".$myJSON."',posted_by,0 from comments where ans_id = ".$ans_id." and posted_by <> '".$_SESSION['user']."' and posted_by <> '".$posted_by."' and posted_by <> '".$q_post_by."'";
		$stmt_push_notifications_1 = $conn->prepare($sql_push_notifications_1);
		$stmt_push_notifications_1->execute();
			
		/* delete old notifications for user who are engaged  */			
		/*
		$sql_remove_old_notify = "select count(1) as count from notifications where user_id = '".$posted_by."'";
		$stmt_remove_old_notify = $conn->prepare($sql_remove_old_notify);
		$stmt_remove_old_notify->execute();
		$result_1 = $stmt_remove_old_notify->fetch();
		$count_1 = $result_1['count'];
		
		if($count_1 > 100)	{
			$net_count = $count_1 - 100;
			$sql_final_remove = "delete from notifications where condition limit ".$net_count." order by created_ts asc";
		}	
		*/
		/* end of delete */
		
	}
	catch(PDOException $e)	{
			echo $e->getMessage();
	}
}
catch(PDOException $e)	{
	echo "Internal server error".$e->getMessage();
}

try	{
	$sql_fetch_comment="select comment_desc,posted_by,created_ts from comments where ans_id=".$ans_id." order by created_ts asc";
	foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
		$comment=$row_cmnt['comment_desc'];
		$posted_by=$row_cmnt['posted_by'];
		$created_ts=$row_cmnt['created_ts'];
		echo '<div class="user-comment-sec">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date(convert_utc_to_local($created_ts)).'</span></div>';
	}
}
catch(PDOException $e)	{
	echo "Internal server error";
}


?>
