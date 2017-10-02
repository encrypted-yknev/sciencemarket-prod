<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";
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
		$notify_text = addslashes("<a href = 'qstn_ans.php?qid=".$qid."'  class='list-group-item' >".$_SESSION['user']." posted an answer to your question on <strong>".$qstn_title."</strong></a>");
		
		if($posted_by != $_SESSION['user'])	{
			try		{
				$sql_push_notifications = "insert into notifications(notify_text,user_id,view_flag)
											values ('".$notify_text."','".$posted_by."',0)";
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

try	{
	$sql_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts,last_updt_ts from answers where qstn_id=".$qid." order by created_ts desc";
	foreach($conn->query($sql_ans) as $row_ans)	{
		$ansid=$row_ans["ans_id"];
		$upvotes=$row_ans["up_votes"];
		$downvotes=$row_ans["down_votes"];
		$createdts=$row_ans["created_ts"];
		$postedby=$row_ans["posted_by"];

		
		$sql_fetch_img="select pro_img_url from users where user_id='".$postedby."'";
		$stmt=$conn->prepare($sql_fetch_img);
		$stmt->execute();
		$result=$stmt->fetch();
		$image=$result['pro_img_url'];
	?>	
	<div class="ans-section" id="user-answer-<?php echo $ansid; ?>">
		<div class="ans-user-img" style="background-image:url('<?php echo $image; ?>'); background-size:cover;"></div>
		<div class="auth-time-section">
			<?php echo $postedby." ".get_user_date(convert_utc_to_local($createdts)); ?>
		</div>
		</br>
		<div class="main-ans-block">
			<?php echo $row_ans["ans_desc"]; ?>
		</div>
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
			<span class="glyphicon glyphicon-thumbs-up"></span>
			<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
			<?php } ?>
		</span>
		<a class="comment-link" href="javascript:void(0)" onclick="showComment('comment-box-<?php echo $ansid; ?>')">Comment</a></br>
		<div class="comment-box" id="comment-box-<?php echo $ansid; ?>"></br>	
			<textarea class="form-control" rows="2" id="comment-<?php echo $ansid; ?>" placeholder="Your comment goes here..."></textarea></br>
			<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
			onclick="addComment(<?php echo $ansid; ?>,document.getElementById('comment-<?php echo $ansid; ?>').value,'comment-area-<?php echo $ansid; ?>')">Comment</button>
			</br>
			<div class="col-sm-11" id="comment-area-<?php echo $ansid; ?>">
			<?php
				try	{
					$sql_fetch_comment="select comment_desc,posted_by from comments where ans_id=".$ansid;
					foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
						$comment=$row_cmnt['comment_desc'];
						$posted_by=$row_cmnt['posted_by'];
						
						echo '<div class="user-comment-sec">'.$comment.' - <span style="font-weight:bold;">'.$posted_by.'</span></div>';
					}
				}
				catch(PDOException $e)	{
					echo "Internal server error";
				}
			?>
			</div>
		</div>
	</div></br>
	<?php
	}
}
catch(PDOException $e)	{
	echo "Some error occured ".$e->getMessage();
}
	?>
