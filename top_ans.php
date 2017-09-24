<?php 
		try	{
			$sql_show_some_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts from answers where 
								   qstn_id = ".$qid." order by up_votes desc,down_votes asc limit 5";
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
					<div class="ans-hidden-top-sec" id="ans-hidden-top-sec-<?php echo $ansid; ?>">
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
							<input type="hidden" id="upvote-value-top-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
							<input type="hidden" id="downvote-value-top-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
							
						<div class="voting-links">
							<span class="vote-sec">
						<?php 
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-value-top-ans-<?php echo $ansid; ?>').value,1)">
								<span id="glyph-up-top-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
							<span id="up-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
							<?php } 
										else	{
									?>
								<span class="glyphicon glyphicon-thumbs-up"></span>
								<span id="up-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
								<?php } ?>
						</span>
						<span class="vote-sec">
						<?php 
									if($ans_user != $_SESSION['user'])	{
								?>
							<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-value-top-ans-<?php echo $ansid; ?>').value,1)">
								<span id="glyph-down-top-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
							<span id="down-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
						<?php } 
									else	{
								?>
							<span class="glyphicon glyphicon-thumbs-down"></span>
							<span id="down-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
							<?php } ?>
						</span>&nbsp;&nbsp;
						<a id="comment-top-link-<?php echo $ansid; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(1,<?php echo $ansid; ?>)">Show comments</a>
						</div>
						<div class="comment-section" id="comment-top-<?php echo $ansid; ?>">
						</br>
						<input type="text" class="form-control comment-inp" id="comment-top-ans-<?php echo $ansid; ?>" placeholder="Leave comment" 
						onkeypress=""/>
						
						</br>
						<button type="button" class="btn btn-primary" style="padding: 1px 2px;" 
						onclick="addComment(1,<?php echo "'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$posted_by."'"; ?>)">Comment</button></br></br>
						
						<div id="comment-area-top-<?php echo $ansid; ?>" style="margin-left:30px;margin-right:30px;border-left:2px solid #195971;background-color:#F7F7F7;border-top:1px solid #F3F3F3;border-bottom:1px solid #F3F3F3;border-right:1px solid #F3F3F3;">
						<?php
							try	{
								$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid;
								foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
									$comment_id=$row_cmnt['comment_id'];
									$comment=$row_cmnt['comment_desc'];
									$posted_by=$row_cmnt['posted_by'];
									$created_ts = $row_cmnt['created_ts'];
									echo '<div class="user-comment-sec" id="comment-top-'.$comment_id.'">'.$comment.' - <strong>'.$posted_by.'</strong>&nbsp;&nbsp;<span class="time-sec">'.get_user_date($created_ts).'</span></div>';
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
				} ?>
			<?php 
			}
		/* 	else	{
				echo "<div class='no-ans-section'>No answers to this question yet. Be the first one to answer.
						<a href='".$slashes."qstn_ans.php?qid=".$qid."'>Click here</a></div>";
			} */
				
		}
		catch(PDOException $e) {
			echo "Some error occured. We are working on it and will get back to you. Sorry for the inconvenience caused ";
		}
		try	{
			$ans_id_list=array();
			$ans_id_str="";
			$sql_store_ansid="select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts from answers where 
								   qstn_id = ".$qid." order by up_votes desc,down_votes asc";
			foreach($conn->query($sql_store_ansid) as $row_store_ansid)
				array_push($ans_id_list,$row_store_ansid['ans_id']);
			$ans_id_str=implode($ans_id_list,"|");
		}
		catch(PDOException $e) {
		}
	?>
	<input id="ans-top-list-qid-<?php echo $qid; ?>" type="hidden" value="<?php echo $ans_id_str; ?>" />
	<input id="scroll-top-flag-<?php echo $qid; ?>" type="hidden" value="1" />
