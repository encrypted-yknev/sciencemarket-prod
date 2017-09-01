<?php 
		try	{
			$sql_show_some_ans = "select ans_id,ans_desc,up_votes,down_votes,posted_by,created_ts from answers where 
								   qstn_id = ".$qid." order by up_votes desc,down_votes asc limit 4";
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
							<input type="hidden" id="upvote-value-top-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
							<input type="hidden" id="downvote-value-top-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
							
						<div class="voting-links">
							<span class="vote-sec">
						<?php 
									if($ans_user != $_SESSION['user'])	{
								?>
							<a href="javscript:void(0)" class="vote-link-area" 
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-value-ans-<?php echo $ansid; ?>').value,1)">
								<span id="glyph-up-top-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
							<span id="up-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></a>
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
							<a href="javscript:void(0)" class="vote-link-area" 
								onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-value-ans-<?php echo $ansid; ?>').value,1)">
								<span id="glyph-down-top-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
							<span id="down-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></a>
						<?php } 
									else	{
								?>
							<span class="glyphicon glyphicon-thumbs-down"></span>
							<span id="down-vote-top-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
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
	</br>
	<div style="font-size:12px; color:#65A668;" class "ans-msg" id="ans-msg-<?php echo $qid; ?>" ></div>
	<div class="user-ans-section">
		<input type="text" class="form-control ans-inp" id="ans-<?php echo $qid; ?>" placeholder="Your answer here" 
		onkeypress="postAnswer(event,'<?php echo $slashes; ?>',this.value,<?php echo $qid.",'".$posted_by."'"; ?>,1)"/>
		</br>
	</div>
