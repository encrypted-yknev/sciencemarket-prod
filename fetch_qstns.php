<?php 
session_start();

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

$qstn_list="";
if(isset($_REQUEST['qstn_list']))	{
	$qstn_list=explode("|",$_REQUEST['qstn_list']);
}
if(isset($_REQUEST['root']))	{
	$slashes=$_REQUEST['root'];
}
if(isset($_REQUEST['questions']))	{
	$qstn_count=$_REQUEST['questions'];
}
$array_len=sizeof($qstn_list);

if($qstn_count >= $array_len)	{
	echo "0";
	return;
}
$start_qstn=$end_qstn=0;
if($qstn_count < $array_len)  {
	$start_qstn = $qstn_count;
}

if($qstn_count+10 > $array_len)	{
	$end_qstn = $array_len - 1;
}
else	{
	$end_qstn = $qstn_count+9;
}

while($start_qstn <= $end_qstn)	{
	$sql="select a.qstn_id,
				 a.qstn_titl,
				 a.qstn_desc,
				 a.posted_by,
				 a.topic_id,
				 a.up_votes,
				 a.down_votes,
				 a.created_ts 
		 from questions a
		 where a.qstn_id = ".$qstn_list[$start_qstn];
	$stmt=$conn->prepare($sql);
	$stmt->execute();
							
	if($stmt->rowCount() > 0)	{
		
		while($row = $stmt->fetch())	{
			$qid=$row['qstn_id'];
			$posted_by=$row['posted_by'];
			$created_ts=$row['created_ts'];
			$topic_id=$row['topic_id'];
			$up_votes=$row['up_votes'];
			$down_votes=$row['down_votes'];
				?>
				<div class="qstn_row">
				<div class="qstn-topic-section">
					<?php
						try	{
							$sql_fetch_topic = "select topic_desc,parent_topic from topics where topic_id = ".$topic_id;
							$stmt_fetch_topic=$conn->prepare($sql_fetch_topic);
							$stmt_fetch_topic->execute();
							$res_topic=$stmt_fetch_topic->fetch();
							
							$topic_desc=$res_topic['topic_desc'];
							$parent_topic=$res_topic['parent_topic'];
							
							$sql_fetch_parent_topic = "select topic_desc from topics where topic_id=".$parent_topic;
							$stmt_fetch_par_topic=$conn->prepare($sql_fetch_parent_topic);
							$stmt_fetch_par_topic->execute();
							$res_par_topic=$stmt_fetch_par_topic->fetch();
							
							$par_topic_desc = $res_par_topic['topic_desc'];
							echo $par_topic_desc." - ".$topic_desc;
						}
						catch(PDOException $e)	{
							
						}
						
						try	{
							$sql_fetch_votes="select pro_img_url,up_votes,down_votes from users where user_id='".$posted_by."'";
							foreach($conn->query($sql_fetch_votes) as $row_user)
								$img_url=$slashes.$row_user["pro_img_url"];
								$up_user_votes=$row_user["up_votes"];
								$down_user_votes=$row_user["down_votes"];
						}
						catch(PDOException	$e)	{
							echo "Error fetching user votes!</br>";
						}
					
					?>
				</div>
				<div class="user-img-section" style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;">
					
				</div>
				<div class="auth-section">
					<?php
						echo $posted_by." . ".get_user_date($created_ts);
					?>
				</div></br>
				<a class="titl-link" href="<?php echo $slashes.'qstn_ans.php?qid='.$qid ?>"><?php echo $row["qstn_titl"]; ?></a>&emsp;
				<span id="qstn-ans-count"></span>
				<p id="qstn-desc"><?php echo $row["qstn_desc"]; ?></p>
		
				<div class="tag-section">
				<?php
					try	{
						$sql_fetch_qstn_tags="select a.tag_name 
											  from tags a,qstn_tags b 
											  where a.tag_id=b.tag_id 
											  and b.qstn_id=".$qid;
						foreach($conn->query($sql_fetch_qstn_tags) as $row_tags)
							echo '<span class="tag-name-section">'.$row_tags['tag_name'].'</span>';
					}
					catch(PDOException	$e)	{
						echo "Error fetching question tags!</br>";
					}
				?>
				</div>
				</br>
				<?php
					$upvotes_id='up-vote-qstn-'.$qid;
					$downvotes_id='down-vote-qstn-'.$qid;
				?>
				<!--
				<button type="button" class="btn btn-primary" onclick="window.location.href='<?php #echo $slashes; ?>qstn_ans.php?qid=<?php #echo $qid; ?>'" style="padding: 1px 2px; font-size:13px;">Answer</button>
				-->
				
				<?php
					$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=0 and post_id=".$qid;
					$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=1 and post_id=".$qid;
					$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
					$stmt_check_up_vote->execute();
					$sql_row_0 = $stmt_check_up_vote->fetch();
					$count_row_0 = $sql_row_0['vote_count'];
					$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
					$stmt_check_down_vote->execute();
					$sql_row_1 = $stmt_check_down_vote->fetch();
					$count_row_1 = $sql_row_1['vote_count'];
						
					?>
				<input type="hidden" id="upvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_0; ?>" />
				<input type="hidden" id="downvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_1; ?>" />
				<span class="vote-sec" id="up-link">
				<?php 
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="up-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',0,'".$slashes."'";?>,document.getElementById('upvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-up-<?php echo $qid; ?>" 
						class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-upvoted":""; ?>"></span>
					<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span></span>
					<?php } 
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-up"></span>
						<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
						<?php } ?>
				</span>
				
				<span class="vote-sec" id="down-link">
				<?php 
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="down-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',1,'".$slashes."'";?>,document.getElementById('downvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-down-<?php echo $qid; ?>" class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-downvoted":""; ?>"></span>
					<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span></span>
				
				<?php } 
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-down"></span>
						<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
						<?php } ?>
				</span>
				
				&nbsp;&nbsp;
				<span class="ans-toggle" id="ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns(<?php echo $qid; ?>,0)">Recent answers</a></span>&nbsp;&nbsp;
				<span class="ans-toggle" id="top-ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns(<?php echo $qid; ?>,1)">Top answers</a></span>
				
				</br></br>
				<div id="front-top-qstn-<?php echo $qid; ?>">
				<?php
					try	{
						$sql_fetch_top_ans="select a.ans_id,
												   a.ans_desc,
												   a.up_votes,
												   a.down_votes,
												   a.posted_by,
												   a.created_ts 
										   from answers a
										   inner join questions b 
										   on a.qstn_id=b.qstn_id
										   left outer join comments c
										   on a.ans_id=c.ans_id
										   where b.qstn_id=".$qid."
										   order by a.created_ts desc,c.created_ts desc
										   limit 2";
						$stmt_fetch_top=$conn->prepare($sql_fetch_top_ans);
						$stmt_fetch_top->execute();
						if($stmt_fetch_top->rowCount() > 0)	{
							
							while($row_top_2 = $stmt_fetch_top->fetch())	{
								
								$ansid=$row_top_2['ans_id'];
								$ans = $row_top_2['ans_desc'];
								$ans_user=$row_top_2['posted_by'];
								$ans_ts=$row_top_2['created_ts'];
								$upvotes=$row_top_2["up_votes"];
								$downvotes=$row_top_2["down_votes"];
								$sql_get_user_pic = "select pro_img_url from users where user_id='".$ans_user."'";
								
								$stmt_get_user_pic = $conn->prepare($sql_get_user_pic);
								$stmt_get_user_pic->execute();
								$row_pic = $stmt_get_user_pic->fetch();
								$ans_user_pic = $slashes.$row_pic['pro_img_url'];
								?>
								<div class="ans-front-hidden-sec" id="ans-front-sec-<?php echo $ansid; ?>">
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
										<input type="hidden" id="upvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
										<input type="hidden" id="downvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
										
									<div class="voting-links">
										<span class="vote-sec">
									<?php 
												if($ans_user != $_SESSION['user'])	{
											?>
										<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
											onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
											<span id="glyph-front-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
										<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
										<?php } 
													else	{
												?>
											<span class="glyphicon glyphicon-thumbs-up"></span>
											<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
											<?php } ?>
									</span>
									<span class="vote-sec">
									<?php 
												if($ans_user != $_SESSION['user'])	{
											?>
										<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
											onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
											<span id="glyph-front-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
										<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
									<?php } 
												else	{
											?>
										<span class="glyphicon glyphicon-thumbs-down"></span>
										<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
										<?php } ?>
									</span>
									&nbsp;&nbsp;
									<a id="comment-link-<?php echo $ansid; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(2,<?php echo $ansid; ?>)">Show comments</a>
									</div>
									<div class="comment-section" id="comment-front-<?php echo $ansid; ?>">
									</br>
									<input type="text" class="form-control comment-inp" id="comment-front-ans-<?php echo $ansid; ?>" placeholder="Leave comment" 
									onkeypress=""/>
									
									</br>
									<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
									onclick="addComment(2,<?php echo "'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$posted_by."'"; ?>)">Comment</button></br></br>
									
									<div id="comment-area-front-<?php echo $ansid; ?>" style="margin-left:30px;margin-right:30px;border-left:2px solid #195971;background-color:#F7F7F7;border-top:1px solid #F3F3F3;border-bottom:1px solid #F3F3F3;border-right:1px solid #F3F3F3;">
									<?php
										try	{
											$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid;
											foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
												$comment_id=$row_cmnt['comment_id'];
												$comment=$row_cmnt['comment_desc'];
												$posted_by=$row_cmnt['posted_by'];
												$created_ts = $row_cmnt['created_ts'];
												echo '<div class="user-comment-sec" id="comment-front-'.$comment_id.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date($created_ts).'</span></div>';
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
						else	{
							echo "<div class='no-ans-section'>No answers to this question yet. Be the first one to answer.</div>";
						}
						
					}
					catch(PDOException $e)	{
						echo "Somer error occurred";
					}
				?>
					
				</div>
				<div class="toggle-ans-sec" id="toggle-ans-sec-<?php echo $qid; ?>" onscroll="fetchAnswers(<?php echo $qid; ?>,'<?php echo $slashes; ?>','r','<?php echo $posted_by; ?>')" >
					<?php include "recent_ans.php"; ?>
				</div>
				<div class="toggle-top-ans-sec" id="toggle-top-ans-sec-<?php echo $qid; ?>" onscroll="fetchAnswers(<?php echo $qid; ?>,'<?php echo $slashes; ?>','t','<?php echo $posted_by; ?>')" >
					<?php include "top_ans.php"; ?>
				</div>
				</br>
				<div style="font-size:14px;color:#65A668;" class "ans-msg" id="ans-msg-<?php echo $qid; ?>" ></div>
				<div class="ans-load" id="ans-load-<?php echo $qid; ?>">
					<img src="<?php echo $slashes; ?>img/loader.gif" height="40" width="40" />
				</div>
				<div class="user-ans-section">
					<input type="text" class="form-control ans-inp" id="ans-<?php echo $qid; ?>" placeholder="Your answer here" 
					onkeypress="postAnswer(event,'<?php echo $slashes; ?>',this.value,<?php echo $qid.",'".$posted_by."'"; ?>,0)"/>
					</br>
				</div>
				</div></br>
				<?php 
					}
	}
	$start_qstn+=1;
}


	?>
