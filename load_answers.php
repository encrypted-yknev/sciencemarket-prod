<?php
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
$slashes="";
function convert_utc_to_local($utc_timestamp)	{
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		if(isset($_COOKIE['user_tz']))
			$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));	
		else
			$date_utc->setTimeZone(new DateTimeZone('UTC'));	
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		echo 'Some error occurred';
	}
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
	
	if($date == date('d') and $month == date('m') and $year == date('Y') and substr($time,11,5) == date("H:i"))
		return 'few seconds ago';
	else if($date == date('d') and $month == date('m') and $year == date('Y'))
		return 'Today '.substr($time,11,5);
	
	return $mth_str.' '.$date.', '.$year;
	
}
function get_time_diff($timestamp_ans)	{
	$timestamp_cur=date("Y-m-d H:i:sa");
	
	$year1=substr($timestamp_ans,0,4);
	$month1=substr($timestamp_ans,5,2);
	$day1=substr($timestamp_ans,8,2);
	$hr1=substr($timestamp_ans,11,2);
	$min1=substr($timestamp_ans,14,2);
	$sec1=substr($timestamp_ans,17,2);


	$year2=substr($timestamp_cur,0,4);
	$month2=substr($timestamp_cur,5,2);
	$day2=substr($timestamp_cur,8,2);
	$hr2=substr($timestamp_cur,11,2);
	$min2=substr($timestamp_cur,14,2);
	$sec2=substr($timestamp_cur,17,2);
	
	if($year1 == $year2)	{
		if($month1 == $month2)	{
			if($day1 == $day2)	{
				if($hr1 == $hr2)	{
					if($min1 == $min2)	{
						if($sec1 == $sec2)	{
							$value=0;	
							$string="seconds";
						}
						else{
							$diff_sec=(int)$sec2-(int)$sec1;
							$value=$diff_sec;	
							$string="seconds";
						}
					}
					else{
						$diff_min=(int)$min2-(int)$min1;
						$value=$diff_min;
						$string="minutes";
					}
				}
				else{
					$diff_hr=(int)$hr2-(int)$hr1;
					$value=$diff_hr;
					$string="hours";
				}
			}
			else	{
				$diff_day=(int)$day2-(int)$day1;
				$value=$diff_day;
				$string="days";
			}
		}
		else	{
			$diff_mon=(int)$month2-(int)$month1;
			$value=$diff_mon;
			$string="months";
		}
	}
	if($value==1)
		$string=substr($string,0,strlen($string)-1);
	return $value.' '.$string.' ago';
}

$user_ans=trim($_REQUEST["ans"]);
$qid=$_REQUEST["qid"];
$posted_by=$_REQUEST["postedBy"];

if(!empty($user_ans))	{
	#$user_ans=htmlspecialchars(trim($ans_desc),ENT_QUOTES);
	try	{
		$sql_get_qstn_titl = "select qstn_titl from questions where qstn_id = ".$qid;
		foreach($conn->query($sql_get_qstn_titl) as $row_titl)
			$qstn_title = $row_titl['qstn_titl'];
		$sql_ans_ins="insert into answers (ans_desc,qstn_id,posted_by) 
						values ('".$user_ans."',".$qid.",'".$_SESSION["user"]."')";
		$conn->exec($sql_ans_ins);
		
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
			try		{
				$sql_push_notifications = "insert into notifications(notify_confg,user_id,view_flag)
											values ('".$myJSON."','".$posted_by."',0)";
				$stmt_push_notify = $conn->prepare($sql_push_notifications);
				$stmt_push_notify->execute();
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
		
		$sql_push_notifications_1 = "insert into notifications(notify_confg,user_id,view_flag)
								   select distinct '".$myJSON."',posted_by,0 from answers where qstn_id = ".$qid." and posted_by <> '".$_SESSION['user']."' and posted_by <> '".$posted_by."'";
		$stmt_push_notifications_1 = $conn->prepare($sql_push_notifications_1);
		$stmt_push_notifications_1->execute();
	}
	catch(PDOException $e)	{
		echo "Error posting answer ".$e->getMessage();
	}
}
else 	{
	echo "Please write some answer";
}

try	{
	$sql_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts,last_updt_ts from answers where qstn_id=".$qid." order by created_ts desc";
	foreach($conn->query($sql_ans) as $row_ans)	{
		$ansid=$row_ans["ans_id"];
		$ans_desc=$row_ans["ans_desc"];
		$upvotes=$row_ans["up_votes"];
		$downvotes=$row_ans["down_votes"];
		$createdts=$row_ans["created_ts"];
		$postedby=$row_ans["posted_by"];
		$user_id_fetch=$postedby;
		include "fetch_user_dtls.php";
	?>	
	<div class="ans-section" id="user-answer-<?php echo $ansid; ?>">
		<div class="ans-user-img" 
			onmouseleave='showUserCard(event,1,<?php echo $ansid; ?>,"a")' 
			onmouseenter='showUserCard(event,0,<?php echo $ansid; ?>,"a")' style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;"></div>
		<div class="auth-time-section">
		<?php
			echo "<span id='ans-posted-".$ansid."' 
			onmouseleave='showUserCard(event,1,".$ansid.",\"a\")' 
			onmouseenter='showUserCard(event,0,".$ansid.",\"a\")'><a href='profile.php?user=".$postedby."'>".$postedby."</a></span> . ".
			get_user_date(convert_utc_to_local($createdts));
		?>		
		</div></br>
		<?php 
			$msg_div_id = "msg-a-".$ansid;
			$post_type="a";
			$user_card=$postedby;
			$up_vote=$upvotes;
			$down_vote=$downvotes;
			$id=$ansid;
			include $slashes."user_card.php"; 
			include $slashes."message_box.php";
		?>
		<div class="main-ans-block">
			<?php echo $ans_desc; ?>
		</div></br>
		<?php 
			$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
								and post_type='A' and vote_type=0 and post_id=".$ansid;
			$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
								and post_type='A' and vote_type=1 and post_id=".$ansid;
			$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
			$stmt_check_up_vote->execute();
			$sql_row_0 = $stmt_check_up_vote->fetch();
			$count_row_0 = $sql_row_0['vote_count'];
			$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
			$stmt_check_down_vote->execute();
			$sql_row_1 = $stmt_check_down_vote->fetch();
			$count_row_1 = $sql_row_1['vote_count'];
		?>
		<input type="hidden" id="upvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
		<input type="hidden" id="downvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />

		<span class="vote-sec">
		<?php 
					if($postedby != $_SESSION['user'])	{
				?>
			<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,0,document.getElementById('upvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
			<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
			<?php } 
						else	{
					?>
				<span class="glyphicon glyphicon-thumbs-up"></span>
				<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
				<?php } ?>
		</span>
		<span class="vote-sec">
		<?php 
					if($postedby != $_SESSION['user'])	{
				?>
			<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
				onclick="increaseCount('<?php echo $ansid."','".$postedby."'";?>,1,document.getElementById('downvote-value-ans-<?php echo $ansid; ?>').value)">
				<span id="glyph-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
		<?php } 
					else	{
				?>
			<span class="glyphicon glyphicon-thumbs-down"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
			<?php } ?>
		</span>
		<a class="comment-link" href="javascript:void(0)" onclick="showComment(<?php echo $ansid; ?>)">View comments</a>
		</br>
		<div class="comment-section" id="comment-front-<?php echo $ansid; ?>">
		</br>
		<div class="comments-list" id="comment-area-front-<?php echo $ansid; ?>" >
			<strong><span id="load-msg-<?php echo $ansid; ?>"></span></strong></br>
			<?php
			try	{
				$comment_array=array();
				$comment_id_str="";
				$sql_fetch_comment_ids="select comment_id from comments where ans_id=".$ansid." order by created_ts asc";
				$stmt_fetch_comment_ids=$conn->prepare($sql_fetch_comment_ids);
				$stmt_fetch_comment_ids->execute();
				if($stmt_fetch_comment_ids->rowCount() > 0)	{
					while($row = $stmt_fetch_comment_ids->fetch())	{
						$cmt_id=$row['comment_id'];
						array_push($comment_array,$cmt_id);
					}
					$comment_id_str=implode("|",$comment_array);
				}
				
				$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid." order by created_ts asc limit 5";
				$stmt_fetch_comment=$conn->prepare($sql_fetch_comment);
				$stmt_fetch_comment->execute();
				
				if($stmt_fetch_comment->rowCount() > 0)	{
					echo "<div class='cmnt-section' id='cmnt-list-".$ansid."'>";
					while($row_cmnt = $stmt_fetch_comment->fetch())	{
						$comment_id=$row_cmnt['comment_id'];
						$comment=$row_cmnt['comment_desc'];
						$cmnt_posted_by=$row_cmnt['posted_by'];
						$created_ts = $row_cmnt['created_ts'];
						
						echo "<div class='user-comment-sec' id='comment-list-front-".$comment_id."'>".$comment." - <strong>
							<span id='cmn-posted-".$comment_id."' 
							onmouseleave='showUserCard(event,1,".$comment_id.",\"fc\")' 
							onmouseenter='showUserCard(event,0,".$comment_id.",\"fc\")'>
							<a href='".$slashes."profile.php?user=".$cmnt_posted_by."'>".$cmnt_posted_by."</a></span></strong>&nbsp;&nbsp;
							<span class='time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span></div>";
						
						$user_id_fetch=$cmnt_posted_by;
						
						include $slashes."fetch_user_dtls.php";
						
						$post_type="fc";
						$id=$comment_id;
						$user_card=$cmnt_posted_by;
						$up_vote=$up_user_votes;
						$down_vote=$down_user_votes;
						include $slashes."user_card.php"; 
						
					}
					echo "</div>";
				}
				else	{
					echo "<span style='margin-left:10px;font-size:13px; color:#626262;'>No comments in this answer yet</span>";
				}
			}
			catch(PDOException $e)	{
				echo "Internal server error";
			}
		?>
		<?php
			$comment_count = $stmt_fetch_comment_ids->rowCount();
			if($comment_count > 5)
				echo "<span id='comment-load-front-text-".$ansid."' href='javascript:void(0)' onclick='loadMoreComments(".$ansid.")' class='show-comment-text' style='margin-left:10px;font-size:12px; color:#626262; text-decoration:underline;'>View more comments...</span></br>";
		?></br>
		<input class="form-control comment-textbox" id="comment-front-ans-<?php echo $ansid; ?>" onfocus="showAlert(0,<?php echo $logged_in;?>)" placeholder="Your comment goes here..." onkeypress="addComment(event,<?php echo $ansid.",'".$postedby."',".$qid.",'".$posted_by."'"; ?>)" style="margin-left:10px;"/>
		</br>
		</div></br>
		<input id="cid-front-section-<?php echo $ansid; ?>" type="hidden" value="<?php echo $comment_id_str; ?>"/>
		</div>
	</div></br>
	<?php
	}
}
catch(PDOException $e)	{
	echo "Some error occured ".$e->getMessage();
}
?>
	</div>
</div>
 
