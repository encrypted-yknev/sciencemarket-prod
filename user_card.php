<div class="user-card-section" id="user-card-<?php echo $post_type."-".$id; ?>" onmouseenter="showUserCard(event,2,<?php echo $id; ?>,'<?php echo $post_type; ?>')" onmouseleave="showUserCard(event,1,<?php echo $id; ?>,'<?php echo $post_type; ?>')">
	<div class="card-top-bar">
		<div class="user-name-card">
			<span class="userid-text">userid : <a style="color:#fff;" href="<?php echo $slashes; ?>profile.php"><?php echo $user_card; ?></a></span>
		</div>
	</div>
	<div class="row">
	<div class="col-sm-4">
		<a href="<?php echo $slashes; ?>profile.php?user=<?php echo $user_card; ?>">
		<div class="user-card-image" style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;" >
		</div>
		</a>
	</div>
	<div class="col-sm-8 right-column">
	<!--<p class="about-user-card"><?php #echo $desc; ?></p>
	<div class="card-more-text"><a class="">more</a></div></br>-->
	<div class="card-disp-name">
		<strong><?php echo $disp_name; ?></strong></br> user stats
		
	</div>
	<div class="vote-section-card">
		<div class="vote-up-card">
			<div class="upvote-logo"></div>&nbsp;
			<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $up_vote; ?></strong></span>
		</div>
		
		<div class="vote-down-card">
			<div class="downvote-logo"></div>&nbsp;
			<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $down_vote; ?></strong></span>
		</div>
	</div></br></br>
	<div class="line-follow-card">
		<?php 
		try	{
			$sql_count_followers="select count(1) as cnt_follower from followers where following_user_id='".$user_card."'";
			$sql_count_following="select count(1) as cnt_following from followers where user_id='".$user_card."'";
			foreach($conn->query($sql_count_followers) as $row_cnt_follow)
				$cnt_folow=$row_cnt_follow['cnt_follower'];
			foreach($conn->query($sql_count_following) as $row_cnt_following)
				$cnt_folowing=$row_cnt_following['cnt_following'];
		}
		catch(PDOException $e)	{
			
		}
		echo "<strong>".$cnt_folow."</strong> ".($cnt_folow>1?'followers':"follower")." | <strong>".$cnt_folowing."</strong> following"; ?>
	</div>
	</div>
	</div>
	<div class="profile-link">
		<a href="<?php echo $slashes; ?>profile.php?user=<?php echo $user_card; ?>">View full profile</a>
	</div>
	<div class="interest-user-card">
	<?php
		try	{
			$sql_fetch_interest = "select tag_name from tags t1 inner join user_tags t2
									on t1.tag_id = t2.tag_id 
									where t2.user_id = '".$user_card."'";
			$stmt_fetch_interest = $conn->prepare($sql_fetch_interest);
			$stmt_fetch_interest->execute();
			$interest_str="";
			if($stmt_fetch_interest->rowCount() > 0)	{
				echo '<strong>Interests</strong> - ';
				while($row_interest=$stmt_fetch_interest->fetch())	{
					$interest_str.="<span>".$row_interest['tag_name'].",</span>&nbsp;";
				}
				$interest_str=trim($interest_str);
				$interest_str=substr($interest_str,0,-1);
				echo $interest_str;
			}
			else	{
				echo "User hasn't added any interests yet";
			}
		}
		catch(PDOException $e)	{
			
		}
	?>
	</div></br>
	<?php if(($logged_in==1 and $user_card!=$_SESSION['user']) or ($logged_in==0))	{
			?>
	<div class="follow-user-card">
		<?php
		try	{
				if($logged_in==1)	{
					$sql_check_follower = "select count(1) as count from followers where user_id='".$_SESSION['user']."' and following_user_id='".$user_card."'";
					$stmt_check_follower = $conn->prepare($sql_check_follower);
					$stmt_check_follower->execute();
					$row_user_count = $stmt_check_follower->fetch();
					$count_follower = $row_user_count['count'];
				}
				else
					$count_follower = 0;
				
				if($count_follower > 0)	{
					$follow_class="btn btn-primary disabled btn-disabled";
					$unfollow_class="btn btn-danger";
					$is_follower=1;
					$click_attr_fol="";
					$click_attr_unfol="onclick='updateFollower(\"".$slashes."\",\"".$user_card."\",1,".$id.",".$logged_in.",\"".$post_type."\")'";
				}
				else	{
					$follow_class="btn btn-primary";
					$unfollow_class="btn btn-danger disabled btn-disabled";
					$is_follower=0;	
					$click_attr_fol="onclick='updateFollower(\"".$slashes."\",\"".$user_card."\",0,".$id.",".$logged_in.",\"".$post_type."\")'";
					$click_attr_unfol="";
				}
			}
			catch(PDOException $e)	{
				
			}
			?>
			<button id="follow-<?php echo $post_type."-".$id; ?>" class="<?php echo $follow_class; ?>"<?php echo$click_attr_fol; ?>>Follow</button>&emsp;
			<button id="unfollow-<?php echo $post_type."-".$id; ?>" class="<?php echo $unfollow_class; ?>" <?php echo$click_attr_unfol; ?>>UnFollow</button>&emsp;
			<button class="btn btn-primary" onclick="showMessageBox(0,'<?php echo $msg_div_id; ?>')">Send message</button>
	</div></br>
	<div class="follow-msg-section" id="follow-message-<?php echo $post_type."-".$id; ?>"></div></br>
	<!--
	<div class="vote-data-card">
		<span class="label label-success label-text">Upvotes&nbsp;<span style="background-color:#fff;color:#5cb85c;margin:2px;padding:1px;"><?php #echo $up_user_votes; ?></span></span>
		<span class="label label-danger label-text">Downvotes&nbsp;<span style="background-color:#fff;color:#d9534f;margin:2px;padding:1px;"><?php #echo $down_user_votes; ?></span></span>
	</div>-->
	<?php	}	?>
	
</div>v
