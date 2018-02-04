<?php 
session_start();

if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
function convert_utc_to_local($utc_timestamp)	{
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		
	}
}
$ans_list="";
if(isset($_REQUEST['qid']))	{
	$qid=$_REQUEST['qid'];
}
if(isset($_REQUEST['ans_list']))	{
	$ans_list=explode("|",$_REQUEST['ans_list']);
}
if(isset($_REQUEST['qstn_user']))	{
	$postedBy=$_REQUEST['qstn_user'];
}
if(isset($_REQUEST['root']))	{
	$slashes=$_REQUEST['root'];
}
if(isset($_REQUEST['answers']))	{
	$ans_count=$_REQUEST['answers'];
}
if(isset($_REQUEST['source_flag']))	{
	$source_flag=$_REQUEST['source_flag'];
}

$id_upvote_val=$id_glyp_val=$id_upvote_ans_val=$id_downvote_val=$id_glyp_val=$id_downvote_ans_val=$id_main_sec="";

$array_len=sizeof($ans_list);

if($ans_count >= $array_len)	{
	echo "0";
	return;
}
$start_ans=$end_ans=0;
if($ans_count < $array_len)  {
	$start_ans = $ans_count;
}

if($start_ans+5 > $array_len)	{
	$end_ans = $array_len - 1;
}
else	{
	$end_ans = $start_ans+4;
}

while($start_ans <= $end_ans)	{
	$sql="select a.ans_id,
				 a.ans_desc,
				 a.posted_by,
				 a.up_votes,
				 a.down_votes,
				 a.created_ts 
		 from answers a
		 where a.ans_id = ".$ans_list[$start_ans];
	$stmt=$conn->prepare($sql);
	$stmt->execute();
							
	if($stmt->rowCount() > 0)	{
		
		while($row_ans = $stmt->fetch())	{
			$ansid=$row_ans['ans_id'];
			$ans = $row_ans['ans_desc'];
			$ans_user=$row_ans['posted_by'];
			$ans_ts=$row_ans['created_ts'];
			$upvotes=$row_ans["up_votes"];
			$downvotes=$row_ans["down_votes"];
			$sql_get_user_pic = "select pro_img_url from users where user_id='".$ans_user."'";
			
			$stmt_get_user_pic = $conn->prepare($sql_get_user_pic);
			$stmt_get_user_pic->execute();
			$row_pic = $stmt_get_user_pic->fetch();
			$ans_user_pic = $row_pic['pro_img_url'];
			
			if($source_flag == 'r')	{
				$id_upvote_val = "upvote-value-ans-".$ansid;
				$id_downvote_val = "downvote-value-ans-".$ansid;
				$id_glyp_up_val = "glyph-up-ans-".$ansid;
				$id_glyp_down_val = "glyph-down-ans-".$ansid;
				$id_upvote_ans_val = "up-vote-ans-".$ansid;
				$id_downvote_ans_val = "down-vote-ans-".$ansid;
				$id_main_sec = "ans-hidden-sec-".$ansid;
				$id_main_cls = "ans-hidden-sec";
				$comment_ans = "comment-ans-".$ansid;
				$comment_area = "comment-area-".$ansid;
				$comment_link = "comment-recent-link-".$ansid;
				$comment_section = "comment-recent-".$ansid;
				$comment_id_name = "comment-list-recent-".$ansid;
				$comment_text1 = "comment-load-recent-text-".$ansid;
				$comment_text2 = "comment-display-recent-text-".$ansid;
				$input_comment_text = "cid-recent-section-".$ansid;
				$comment_list = "cmnt-list-recent-".$ansid;
				$card_ans="ra";
				$card_cmnt="rc";
				$token = 0;
			}
			else	{
				$id_upvote_val = "upvote-value-top-ans-".$ansid;
				$id_downvote_val = "downvote-value-top-ans-".$ansid;
				$id_glyp_up_val = "glyph-up-top-ans-".$ansid;
				$id_glyp_down_val = "glyph-down-top-ans-".$ansid;
				$id_upvote_ans_val = "up-vote-top-ans-".$ansid;
				$id_downvote_ans_val = "down-vote-top-ans-".$ansid;
				$id_main_sec = "ans-hidden-top-sec-".$ansid;
				$id_main_cls = "ans-hidden-top-sec";
				$comment_ans = "comment-top-ans-".$ansid;
				$comment_area = "comment-area-top-".$ansid;
				$comment_link = "comment-top-link-".$ansid;
				$comment_section = "comment-top-".$ansid;
				$comment_id_name = "comment-list-top-".$ansid;
				$comment_text1 = "comment-load-top-text-".$ansid;
				$comment_text2 = "comment-display-top-text-".$ansid;
				$input_comment_text = "cid-top-section-".$ansid;
				$comment_list = "cmnt-list-top-".$ansid;
				$card_ans="ta";
				$card_cmnt="tc";
				$token = 1;
			}
					?>
					<div class="<?php echo $id_main_cls; ?>" id="<?php echo $id_main_sec; ?>">
						<div class="photo-ans-sec" 
						onmouseleave='showUserCard(event,1,<?php echo $ansid; ?>,"<?php echo $card_ans; ?>")' 
						onmouseenter='showUserCard(event,0,<?php echo $ansid; ?>,"<?php echo $card_ans; ?>")'
						style="background-image:url('<?php echo $ans_user_pic; ?>'); background-size:cover;"></div>
							
						<div class="auth-text-section">
							<?php 
							echo "<span id='ans-posted-".$ansid."' onmouseleave='showUserCard(event,1,".$ansid.",\"".$card_ans."\")' onmouseenter='showUserCard(event,0,".$ansid.",\"".$card_ans."\")'><a href='".$slashes."profile.php?user=".$ans_user."'>".$ans_user."</a></span> . ".
							get_user_date(convert_utc_to_local($ans_ts));
							?>
						</br>
						</div></br>
						<?php 
							$user_id_fetch=$ans_user;
							
							include "fetch_user_dtls.php";
							
							$post_type=$card_ans;
							$user_card=$ans_user;
							$up_vote=$up_user_votes;
							$down_vote=$down_user_votes;
							$id=$ansid;
							include "user_card.php"; 
						?>
						<div class="ans-text-section"><?php echo $ans."</br>"; ?></div></br>
						<?php 
						if($logged_in == 1)	{
							$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' and post_type='A' and vote_type=0 and post_id=".$ansid;
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
						}
						else	{
							$count_row_0 = 0;
							$count_row_1 = 0;
						}
							?>
						<input type="hidden" id="<?php echo $id_upvote_val; ?>" value="<?php echo $count_row_0; ?>" />
						<input type="hidden" id="<?php echo $id_downvote_val; ?>" value="<?php echo $count_row_1; ?>" />
							
						<div class="voting-links">
							<span class="vote-sec">
						<?php 
							if($logged_in == 1)	{
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('<?php echo $id_upvote_val; ?>').value,<?php echo ($source_flag == 'r'?0:1); ?>)">
								<span id="<?php echo $id_glyp_up_val; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
							<span id="<?php echo $id_upvote_ans_val; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
							<?php }
								else	{
									?>
								<span class="glyphicon glyphicon-thumbs-up"></span>
								<span id="<?php echo $id_upvote_ans_val; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
								<?php } 
							}
										else	{
									?>
								<span class="glyphicon glyphicon-thumbs-up"></span>
								<span id="<?php echo $id_upvote_ans_val; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
								<?php } ?>
						</span>
						<span class="vote-sec">
						<?php 
						if($logged_in == 1)	{
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('<?php echo $id_downvote_val; ?>').value,<?php echo ($source_flag == 'r'?0:1); ?>)">
								<span id="<?php echo $id_glyp_down_val; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
							<span id="<?php echo $id_downvote_ans_val; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
						<?php }
							else	{
								?>
							<span class="glyphicon glyphicon-thumbs-down"></span>
							<span id="<?php echo $id_downvote_ans_val; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
							<?php } 
						}
									else	{
								?>
							<span class="glyphicon glyphicon-thumbs-down"></span>
							<span id="<?php echo $id_downvote_ans_val; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
							<?php } ?>
						</span>&nbsp;&nbsp;
						<a id="<?php echo $comment_link; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(<?php echo $token; ?>,<?php echo $ansid; ?>)">Show comments</a>
						</div>
						
					<div class="comment-section" id="<?php echo $comment_section;?>">
						</br>
						<div class="comments-list" id="<?php echo $comment_area; ?>">
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
								
								$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid." order by created_ts desc limit 5";
								$stmt_fetch_comment=$conn->prepare($sql_fetch_comment);
								$stmt_fetch_comment->execute();
								if($stmt_fetch_comment->rowCount() > 0)	{
									echo "<div class='cmnt-section' id='".$comment_list."'>";
									while($row_cmnt = $stmt_fetch_comment->fetch())	{
										$comment_id=$row_cmnt['comment_id'];
										$comment=$row_cmnt['comment_desc'];
										$cmnt_posted_by=$row_cmnt['posted_by'];
										$created_ts = $row_cmnt['created_ts'];
										
										echo "<div class='user-comment-sec' id='".$comment_id_name.$comment_id."'>".$comment." - <strong>
										<span id='cmn-posted-".$comment_id."' 
										onmouseleave='showUserCard(event,1,".$comment_id.",\"".$card_cmnt."\")' 
										onmouseenter='showUserCard(event,0,".$comment_id.",\"".$card_cmnt."\")'>
										<a href='".$slashes."profile.php?user=".$cmnt_posted_by."'>".$cmnt_posted_by."</a></span></strong>&nbsp;&nbsp;
										<span class='time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span></div>";
										
										$user_id_fetch=$cmnt_posted_by;
													
										include "fetch_user_dtls.php";
										
										$post_type=$card_cmnt; 
										$id=$comment_id;
										$user_card=$cmnt_posted_by;
										$up_vote=$up_user_votes;
										$down_vote=$down_user_votes;
										include "user_card.php"; 
									}
									echo "</div>";
								}
								else	{
									echo "No comments in this answer yet";
								}
							}
							catch(PDOException $e)	{
								echo "Internal server error";
							}
						?>
						<input type="text" class="form-control comment-inp" id="<?php echo $comment_ans; ?>" placeholder="Leave comment" 
						onkeypress="addComment(event,<?php echo $token.",'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$postedBy."'"; ?>)" onfocus="showAlert(0,<?php echo $logged_in; ?>)"/>
						
						</br>
						</div></br>
						<?php
						$comment_count = $stmt_fetch_comment_ids->rowCount();
						if($comment_count > 5)
							echo "<span id='".$comment_text1."' href='javascript:void(0)' onclick='loadMoreComments(".$token.",\"".$slashes."\",".$ansid.")' class='show-comment-text'>View more comments</span>";
						?>
						<input id="<?php echo $input_comment_text; ?>" type="hidden" value="<?php echo $comment_id_str; ?>"/>
					</div></br>
					</div>
					<?php
				} 
	}
	$start_ans+=1;
}


	?>
