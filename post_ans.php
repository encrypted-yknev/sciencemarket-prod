<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;
include "connectDb.php";
include "forum/functions/get_time.php";
function convert_utc_to_local($utc_timestamp)	{
	$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
	$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
	$date_final = $date_utc->format('Y-m-d H:i:s');
	return $date_final;
}
$ans_desc=$_REQUEST["ans"];
$qid=$_REQUEST["qid"];
$posted_by=$_REQUEST["postedBy"];
$slashes = $_REQUEST['slashes'];
$flag = $_REQUEST['flag'];
if(!empty($ans_desc))	{
	$user_ans=htmlspecialchars(trim($ans_desc),ENT_QUOTES);
	try	{
		$sql_get_qstn_titl = "select qstn_titl from questions where qstn_id = ".$qid;
		foreach($conn->query($sql_get_qstn_titl) as $row_titl)
			$qstn_title = $row_titl['qstn_titl'];
		$sql_ans_ins="insert into answers (ans_desc,qstn_id,posted_by) 
						values ('".$user_ans."',".$qid.",'".$_SESSION["user"]."')";
		$stmt_post_ans = $conn->prepare($sql_ans_ins);
		$stmt_post_ans->execute();
		
		/* Build JSON for notification config for user who posted the question 	*/
			$myObj=array();
			$myObj["post_type"] = "A";
			$myObj["user_id"] = $_SESSION['user'];
			$myObj["ans_config"]["ans_posted_by"] = $_SESSION['user'];
			$myObj["qstn_config"]["qstn_id"] = $qid;
			$myObj["qstn_config"]["qstn_posted_by"] = $posted_by;

			$myJSON = json_encode($myObj);
		/* end of build*/
		
		if($posted_by != $_SESSION['user'])	{
			$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."'  class='list-group-item'>".$_SESSION['user']." posted an answer to your question on <strong>".$qstn_title."</strong></a>");
			
			$sql_push_notifications = "insert into notifications(notify_confg,user_id)
										values ('".$myJSON."','".$posted_by."')";
			$stmt_push_notify = $conn->prepare($sql_push_notifications);
			$stmt_push_notify->execute();
			if($stmt_push_notify->rowCount() > 0)	{
				#$check=true;
				include "disp_ans_enter.php";
			}
			else	{
				#$check=false;
				#include "disp_ans_enter.php";
				echo 0;
			}
		}
		else	{
			if($stmt_post_ans->rowCount() > 0)	{
				#$check=true;
				include "disp_ans_enter.php";
			}
				
			else{
				#$check=false;
				#include "disp_ans_enter.php";
				echo 0;
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
		$sql_push_notifications_1 = "insert into notifications(notify_confg,user_id)
								   select distinct '".$myJSON."',posted_by from answers where qstn_id = ".$qid." and posted_by <> '".$_SESSION['user']."' and posted_by <> '".$posted_by."'";
		$stmt_push_notifications_1 = $conn->prepare($sql_push_notifications_1);
		$stmt_push_notifications_1->execute();
	}
	catch(PDOException $e)	{
		echo -1;
	}
}
else 	{
	echo -2;
}
?>
