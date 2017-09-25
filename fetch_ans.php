<?php 
session_start();

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

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
			$ans_user_pic = $slashes.$row_pic['pro_img_url'];
			
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
				$token = 1;
			}
					?>
					<div class="<?php echo $id_main_cls; ?>" id="<?php echo $id_main_sec; ?>">
						<div class="photo-ans-sec" style="background-image:url('<?php echo $ans_user_pic; ?>'); background-size:cover;"></div>
							
						<div class="auth-text-section">
							<?php echo '<strong>'.$ans_user.'</strong> - <span class="time-sec">'.get_user_date($ans_ts).'</span>'; ?></br>
						</div></br>
						<div class="ans-text-section"><?php echo $ans."</br>"; ?></div></br>
						<?php 
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
								
							?>
						<input type="hidden" id="<?php echo $id_upvote_val; ?>" value="<?php echo $count_row_0; ?>" />
						<input type="hidden" id="<?php echo $id_downvote_val; ?>" value="<?php echo $count_row_1; ?>" />
							
						<div class="voting-links">
							<span class="vote-sec">
						<?php 
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
								<?php } ?>
						</span>
						<span class="vote-sec">
						<?php 
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
							<?php } ?>
						</span>&nbsp;&nbsp;
						<a id="<?php echo $comment_link; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(<?php echo $token; ?>,<?php echo $ansid; ?>)">Show comments</a>
						</div>
						
					<div class="comment-section" id="<?php echo $comment_section;?>">
						</br>
						<input type="text" class="form-control comment-inp" id="<?php echo $comment_ans; ?>" placeholder="Leave comment" 
						onkeypress=""/>
						
						</br>
						<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
						onclick="addComment(<?php echo $token.",'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$postedBy."'"; ?>)">Comment</button></br></br>
						
						<div id="<?php echo $comment_area; ?>" style="margin-left:30px;margin-right:30px;border-left:2px solid #195971;background-color:#F7F7F7;border-top:1px solid #F3F3F3;border-bottom:1px solid #F3F3F3;border-right:1px solid #F3F3F3;">
						<?php
							try	{
								$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid;
								foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
									$comment_id=$row_cmnt['comment_id'];
									$comment=$row_cmnt['comment_desc'];
									$posted_by=$row_cmnt['posted_by'];
									$created_ts = $row_cmnt['created_ts'];
									$comment_id_name = ($source_flag == 'r'?'comment-':'comment-top-').$comment_id;
									echo '<div class="user-comment-sec" id="'.$comment_id_name.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date($created_ts).'</span></div>';
								}
							}
							catch(PDOException $e)	{
								echo "Internal server error";
							}
						?>
						</div>
					</div></br>
					</div>
					<?php
				} 
	}
	$start_ans+=1;
}


	?>
