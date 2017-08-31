<?php 

$url_path = $_SERVER['PHP_SELF'];
$count_slash = substr_count($url_path,"/");
if($count_slash==1)
	$slashes = "";
else if($count_slash==2)
	$slashes = "../";
else if($count_slash==3)
	$slashes = "../../";
else if($count_slash==4)
	$slashes = "../../../";

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
					<a href="javscript:void(0)" class="vote-link-area" id="up-link-area" 
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',0,'".$slashes."'";?>,document.getElementById('upvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-up-<?php echo $qid; ?>" 
						class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-upvoted":""; ?>"></span>
					<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span></a>
					<?php } 
						else	{
					?>
					<span class="badge upvote-badge">
						<span class="glyphicon glyphicon-thumbs-up"></span>
						<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
					</span>
						<?php } ?>
				</span>
				
				<span class="vote-sec" id="down-link">
				<?php 
					if($posted_by != $_SESSION['user'])	{
				?>
					<a href="javscript:void(0)" class="vote-link-area" id="down-link-area" 
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',1,'".$slashes."'";?>,document.getElementById('downvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-down-<?php echo $qid; ?>" class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-downvoted":""; ?>"></span>
					<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span></a>
				
				<?php } 
						else	{
					?>
					<span class="badge downvote-badge">
						<span class="glyphicon glyphicon-thumbs-down"></span>
						<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
					</span>
						<?php } ?>
				</span>
				
				&nbsp;&nbsp;
				<span class="ans-toggle" id="ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" onclick="toggleAns('toggle-ans-sec-<?php echo $qid; ?>')">Answer</a></span>
				
				</br></br>
				<div class="toggle-ans-sec" id="toggle-ans-sec-<?php echo $qid; ?>" >
					
					<div style="font-size:12px; color:#65A668;" class "ans-msg" id="ans-msg-<?php echo $qid; ?>" ></div>
					<?php 
						try	{
							$sql_show_some_ans = "select * from (select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts from answers where 
												   qstn_id = ".$qid." order by created_ts desc limit 4) a order by created_ts asc";
							$sql_count_ans = "select count(1) from answers where qstn_id = ".$qid;
							
							$stmt_show_some_ans=$conn->prepare($sql_show_some_ans);
							$stmt_show_some_ans->execute();
							
							if($stmt_show_some_ans->rowCount() > 0)	{
								
								while($row_ans = $stmt_show_some_ans->fetch())	{
									
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
									?>
									<div class="ans-hidden-sec" id="ans-hidden-sec-<?php echo $ansid; ?>">
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
											<input type="hidden" id="upvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
											<input type="hidden" id="downvote-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
											
										<div class="voting-links">
											<span class="vote-sec">
										<?php 
													if($ans_user != $_SESSION['user'])	{
												?>
											<a href="javscript:void(0)" class="vote-link-area" 
												onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-value-ans-<?php echo $ansid; ?>').value)">
												<span id="glyph-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
											<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></a>
											<?php } 
														else	{
													?>
											<span class="badge upvote-ans-badge">
												<span class="glyphicon glyphicon-thumbs-up"></span>
												<span id="up-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
											</span>
												<?php } ?>
										</span>
										<span class="vote-sec">
										<?php 
													if($ans_user != $_SESSION['user'])	{
												?>
											<a href="javscript:void(0)" class="vote-link-area" 
												onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-value-ans-<?php echo $ansid; ?>').value)">
												<span id="glyph-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
											<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></a>
										<?php } 
													else	{
												?>
										<span class="badge downvote-ans-badge">
											<span class="glyphicon glyphicon-thumbs-down"></span>
											<span id="down-vote-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
										</span>
											<?php } ?>
										</span>
										</div>
									</div>
									<?php
								}
								$sql_count_ans = "select count(1) as ans_count from answers where qstn_id = ".$qid;
								$stmt_count_ans = $conn->prepare($sql_count_ans);
								$stmt_count_ans->execute();
								$res_count=$stmt_count_ans->fetch();
								$answer_count=$res_count['ans_count'];
								if($answer_count <= 3)
									echo '</br><a href="'.$slashes.'qstn_ans.php?qid='.$qid.'">View answers...</a>';
								else
									echo '</br><a href="'.$slashes.'qstn_ans.php?qid='.$qid.'">View '.($answer_count-3).' more answers...</a>';
							}
							else	{
								echo "<div class='no-ans-section'>No answers to this question yet. Be the first one to answer.
										<a href='".$slashes."qstn_ans.php?qid=".$qid."'>Click here</a></div>";
							}
								
						}
						catch(PDOException $e) {
							echo "Some error occured. We are working on it and will get back to you. Sorry for the inconvenience caused ".$e->getMessage();
						}
					?>
					<div class="user-ans-section">
						<input type="text" class="form-control ans-inp" id="ans-<?php echo $qid; ?>" placeholder="Your answer here" 
						onkeypress="postAnswer(event,'<?php echo $slashes; ?>',this.value,<?php echo $qid.",'".$posted_by."'"; ?>)"/>
						</br>
					</div>
				</div>
				</div></br>
				<?php 
					}
	}


	?>