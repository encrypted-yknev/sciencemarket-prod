<?php
session_start();
include "connectDb.php";

if(isset($_REQUEST['notify_typ']))
	$notify_typ = $_REQUEST['notify_typ'];

if(strcmp($notify_typ,"DISPLAY") == 0)	{
	try		{
		$sql_get_notifications = "select notify_id,notify_confg,view_flag from notifications where user_id = '".$_SESSION['user']."' order by created_ts desc";
		$stmt_get_notify = $conn->prepare($sql_get_notifications);
		$stmt_get_notify->execute();
		
		if($stmt_get_notify->rowCount() > 0)	{
			while($row_notify = $stmt_get_notify->fetch())	{
				$user_notify_text = $row_notify['notify_confg'];
				$user_notify_id = $row_notify['notify_id'];
				$user_view_flag = (int)$row_notify['view_flag'];
				
				$user_notify_array=json_decode($user_notify_text,true);
				/*
				JSON format
				commentsJSON - 
				
				{
					"post_type":"C",
					"user_id":null,
					"ans_config":
					{
						"ans_id":0,
						"ans_posted_by":null
					},
					"qstn_config":
					{
						"qstn_id":0,
						"qstn_posted_by":null
					}
				}
				
				answersJSON - 
				
				{
					"post_type":"A",
					"user_id":null,
					"ans_config":
					{
						"ans_id":0,
						"ans_posted_by":null
					},
					"qstn_config":
					{
						"qstn_id":0,
						"qstn_posted_by":null
					}
				}
				
				*/
				try	{
					
					$qid=$user_notify_array['qstn_config']['qstn_id'];
					$qstn_posted_by=$user_notify_array['qstn_config']['qstn_posted_by'];
					$sql_fetch_qstn_titl="select qstn_titl from questions where qstn_id=".$qid;
					foreach($conn->query($sql_fetch_qstn_titl) as $row_qstn)
						$qstn_titl_notify=$row_qstn['qstn_titl'];
					$ans_posted_by=$user_notify_array['ans_config']['ans_posted_by'];
							
					if($user_notify_array['post_type'] == 'A')	{
						$a_link = "qstn_ans.php?qid=".$qid;
						
						if($_SESSION['user'] == $qstn_posted_by)	{
							$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> answered your question on <strong>'.$qstn_titl_notify.'</strong>';
						}
						else if($user_notify_array['user_id'] != $qstn_posted_by)	{
							$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> answered <strong>'.$qstn_posted_by.'\'s</strong> question on <strong>'.$qstn_titl_notify.'</strong>';
						}
						else {
							$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> answered a question on <strong>'.$qstn_titl_notify.'</strong>';
						}
					}
					else if($user_notify_array['post_type'] == 'C')	{
						$ansid=$user_notify_array['ans_config']['ans_id'];
						$a_link = "qstn_ans.php?qid=".$qid."#user-answer-".$ansid;
						
						if($_SESSION['user'] == $qstn_posted_by and $_SESSION['user'] == $ans_posted_by)	{
								$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on your answer to your question on <strong>'.$qstn_titl_notify.'</strong>';
						}
						else if($_SESSION['user'] != $qstn_posted_by and $_SESSION['user'] == $ans_posted_by){
							if($user_notify_array['user_id'] != $qstn_posted_by)
								$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on your answer to <strong>'.$qstn_posted_by.'\'s</strong> question on <strong>'.$qstn_titl_notify.'</strong>';
							else
								$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on your answer to question on <strong>'.$qstn_titl_notify.'</strong>';
						}
						else if($_SESSION['user'] == $qstn_posted_by and $_SESSION['user'] != $ans_posted_by){
							if($user_notify_array['user_id'] != $ans_posted_by)
								$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on '.$ans_posted_by.'\'s answer to your question on <strong>'.$qstn_titl_notify.'</strong>';
							else
								$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on an answer to your question on <strong>'.$qstn_titl_notify.'</strong>';
						}
						else	{
							if($qstn_posted_by == $ans_posted_by)	{
								if($user_notify_array['user_id'] != $qstn_posted_by)
									$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on an answer to <strong>'.$qstn_posted_by.'\'s</strong> question on <strong>'.$qstn_titl_notify.'</strong>';
								else
									$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on an answer to the question on <strong>'.$qstn_titl_notify.'</strong>';
							}
							else	{
								if($user_notify_array['user_id'] != $qstn_posted_by and $user_notify_array['user_id'] != $ans_posted_by)
									$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on '.$ans_posted_by.'\'s answer to <strong>'.$qstn_posted_by.'\'s</strong> question on <strong>'.$qstn_titl_notify.'</strong>';
								else if($user_notify_array['user_id'] != $qstn_posted_by and $user_notify_array['user_id'] == $ans_posted_by)
									$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on an answer to <strong>'.$qstn_posted_by.'\'s</strong> question on <strong>'.$qstn_titl_notify.'</strong>';
								else if($user_notify_array['user_id'] == $qstn_posted_by and $user_notify_array['user_id'] != $ans_posted_by)
									$notify_text = '<strong>'.$user_notify_array['user_id'].'</strong> commented on '.$ans_posted_by.'\'s answer to a question on <strong>'.$qstn_titl_notify.'</strong>';
							}
						}
					}
				}
				catch(Exception $e)	{
					
				}
				
				if($user_view_flag == 1)
					$notify_div_class = "<a data-id='".$user_notify_id."' id='notify-message-".$user_notify_id."' class='list-group-item' href='".$a_link."' style='min-height:50px;' onclick='updateNotify(this.getAttribute(\"data-id\"))' >";
				else
					$notify_div_class = "<a data-id='".$user_notify_id."' id='notify-message-".$user_notify_id."' class='list-group-item list-view-on' href='".$a_link."' style='min-height:50px;' onclick='updateNotify(this.getAttribute(\"data-id\"))' >";
				
				try	{
					$sql_get_user_pic = "select pro_img_url from users where user_id='".$user_notify_array['user_id']."'";
					foreach($conn->query($sql_get_user_pic) as $row_pic)
						$user_notify_pic = $row_pic['pro_img_url'];
				}
				catch(PDOException $e)	{
					
				}
				
				
				echo $notify_div_class.'<div class="notify-user-pic" style="background-image:url(\''.$user_notify_pic.'\'); background-size:cover;"></div>
				<div class="notify-text-section">'.$notify_text.'</div></a>';
			}
		}
		else
			echo "You don't have any new notifications";
			
	}
	catch(PDOException $e)	{
		echo "Some error occured in fetching notifications. Please try again after some time";
	}
}
else if(strcmp($notify_typ,"UPDATE") == 0)	{
	if(isset($_REQUEST["notify_id"]))
		$notify_id = $_REQUEST["notify_id"];
	try		{
		if($notify_id != -1)
			$sql_updt_notifications = "update notifications set view_flag = 1 where notify_id=".$notify_id;
		else
			$sql_updt_notifications = "update notifications set view_flag = 1 where user_id='".$_SESSION['user']."'";
		$stmt_updt_notification = $conn->prepare($sql_updt_notifications);
		$stmt_updt_notification->execute();
		if($stmt_updt_notification->rowCount() > 0)
			echo 1;
		else
			echo 0;
	}
	catch(PDOException $e)	{
		echo -1;
	}
}



?>
