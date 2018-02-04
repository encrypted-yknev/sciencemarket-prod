<div class="container">
		<div id="side-nav">
			<table border="0">
				<tr>
					<td>
						<div id="nav-id">
							<div class="side-bar"></div>
							<div class="side-bar"></div>
							<div class="side-bar"></div>
						</div>
					</td>
					<td>
						<div id="media-image">
							<img src="img/logo4.svg" width="55" height="55"/>
							<img src="img/logo.svg" width="150" height="50"/>
						</div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>User dashboard</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-1">
						<a href="qstn.php" class="btn btn-info"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;Ask </a>&nbsp;
						<a id="notify-mob" href="javascript:void(0)" class="btn btn-info" ><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp;Notifications</a>
						<div id="show-notify-section-mobile"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-2">
						<input id="srch-qstn-mob" type="text" class="form-control" id="srch-box-media" placeholder="Search questions" onkeypress="fetchQuestionsMobile(this.value,'<?php echo $slashes; ?>')" />
					
						<div id="srch-result-mobile"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<div class="row">
				<div class="col-sm-12" id="pic-row">
					<img src="<?php echo $_SESSION["pro_img"]; ?>" id="side-menu-img" alt="profile image" width="100" height="120"> 
				</div>
			</div></br>

			<div>upvotes   : <span class="badge"><?php echo $_SESSION["up_votes"]; ?></span></div>
			<div>downvotes : <span class="badge"><?php echo $_SESSION["down_votes"]; ?></span></div>
					
			</br>
			<ul class="nav nav-pills nav-stacked">
				<li><a href="profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
				<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
				<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
				<li><a href="logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<div id="head-bottom"></div>
				
			</div>
		</div></br>
		<div class="row">
			<div class="col-sm-2" id="main-side-column" style="background:#FBFBFB;">
				<div class="row" id="first-row">
					<div class="profile-picture">
						<a id="profile-img" href="profile.php" title="Go to my profile">
							<div class="side-user-img" style="background-image:url('<?php echo $_SESSION["pro_img"];?>'); background-size:cover;">
							</div>
						</a>
					</div>
				</div>
				<div class="row" id="second-row">
					<div class="side-data-section">
						</br>
						<div id="vote-section">
							<div class="" id="vote-up">
								<div id="upvote-logo"></div>&nbsp;
								<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $_SESSION["up_votes"]; ?></strong></span>
							</div>
							
							<div class="" id="vote-down">
								<div id="downvote-logo"></div>&nbsp;
								<span class="vote-count-section" style="font-size:12px;"><strong><?php echo $_SESSION["down_votes"]; ?></strong></span>
							</div></br>
						</div>
					</div>
				</div>
				</br>
				<div class="row" id="third-row">
					<div class="col-sm-8">
						<div id="user-data">
							<div class="side-menu-links">Questions</div>
							<div class="side-menu-links">Answers</div>
							<div class="side-menu-links">Followers</div>
							<div class="side-menu-links">Following</div>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="side-menu-links"><?php echo $count_qstn; ?></div>
						<div class="side-menu-links"><?php echo $count_ans; ?></div>
						<div class="side-menu-links">
							<?php	
								$sql_fetch_followers="select count(1) as count_f1 from followers where following_user_id='".$_SESSION['user']."'";
								foreach($conn->query($sql_fetch_followers) as $row_f1)
									echo $row_f1['count_f1'];
							?>
						</div>
						<div class="side-menu-links">
							<?php	
								$sql_fetch_following="select count(1) as count_f2 from followers where user_id='".$_SESSION['user']."'";
								foreach($conn->query($sql_fetch_following) as $row_f2)
									echo $row_f2['count_f2'];
							?>
						</div>
						<div class="side-menu-links"></div>
						<div class="side-menu-links"></div>
					</div>
				</div></br>
				<div class="interest-sec">
					<div id="interest-text">Your interests</div>
					<div id="interest-tabs">
						<?php
							try	{
								$sql_fetch_user_tags="select tag_name from tags t
													  inner join user_tags ut
													  on t.tag_id=ut.tag_id
													  where ut.user_id = '".$_SESSION['user']."'";
								foreach($conn->query($sql_fetch_user_tags) as $result_tags)	{
									echo '<span class="badge">'.$result_tags["tag_name"].'</span>';
								}
							}
							catch(PDOException	$e)	{
								echo "Error fetching interests";
							}
						?>
					</div>
				</div>
			</div>
			<div class="col-sm-7" id="middle-container">
				<?php
				try	{
					$query_string="";
					$sql_fetch_user_interests="select b.tag_name 
									   from user_tags a
									   inner join tags b
									   on a.tag_id=b.tag_id
									   where a.user_id='".$_SESSION['user']."'";
					foreach($conn->query($sql_fetch_user_interests) as $result_user_interest)	{
						$query_string=$query_string.$result_user_interest['tag_name']."|";
					}
					$query_string=substr($query_string,0,strlen($query_string)-1);
					if(strlen(trim($query_string)) != 0)	{
						$sql="select t.qstn_id,
									 t.qstn_titl,
									 t.qstn_desc,
									 t.posted_by,
									 t.up_votes,
									 t.down_votes,
									 t.topic_id,
									 t.created_ts,
									(case when t.answer_ts >= t.comment_ts then t.answer_ts
											else t.comment_ts
									   end) score	
							from 
							(select  a.qstn_id,
									 a.qstn_titl,
									 a.qstn_desc,
									 a.posted_by,
									 a.up_votes,
									 a.down_votes,
									 a.topic_id,
									 a.created_ts,
									coalesce(max(UNIX_TIMESTAMP(d.created_ts)),0) as answer_ts,
									coalesce(max(UNIX_TIMESTAMP(e.created_ts)),0) as comment_ts
							 from questions a 
							   inner join qstn_tags b
							   on a.qstn_id=b.qstn_id
							   inner join tags c 
							   on b.tag_id=c.tag_id 
							   left outer join answers d 
							   on d.qstn_id = a.qstn_id 
							   left outer join comments e 
							   on e.ans_id = d.ans_id
							   where a.posted_by <> '".$_SESSION['user']."' and
							   (c.tag_name REGEXP ('".$query_string."')
							   or a.qstn_titl REGEXP ('".$query_string."')
							   or a.qstn_desc REGEXP ('".$query_string."'))
							   group by a.qstn_id 
							   order by a.created_ts desc) t
							   order by score desc";
							   
								include "forum/fetch_answers1.php";
								if($stmt->rowCount() <=0)	{
									echo '<div class="alert alert-info">
										  We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
									  </div>';
								}
					}
					else
						echo '<div class="alert alert-info">
							  We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
						  </div>';
				}
				catch(PDOException	$e)	{
					echo '';
				}
				?>
			</div>
			<div class="col-sm-3">
				<div id="tags-box">
					<span id="tag-box-title">Popular tags</span></br></br>
					<?php
						try	{
							$sql_fetch_tags_list="  select a.tag_id,a.tag_name,count(1) as cnt_follower
													from tags a
													inner join qstn_tags b 
													on a.tag_id = b.tag_id
													group by a.tag_id,a.tag_name
													order by 3 desc limit 50
												  ";
							$stmt_fetch_tags_list=$conn->prepare($sql_fetch_tags_list);
							$stmt_fetch_tags_list->execute();
							
							if($stmt_fetch_tags_list->rowCount() < 0)	{
								echo "Nothing to show now";
							}
							else	{
								while($row_tags_list=$stmt_fetch_tags_list->fetch())	{
									$tag_id=$row_tags_list['tag_id'];
									$tag_nm=$row_tags_list['tag_name'];
									$count_tags=$row_tags_list['cnt_follower'];
									
									echo "<span class='badge tag-name-list'><a href='forum/index.php?tag=".$tag_id."'>".$tag_nm."</a></span>";
								}
							}
						}
						catch(PDOException $e)	{
							echo "Some error occured in the server";
						}
					
					
					?>
					</br></br>
				</div></br>
			</div>
		</div>
	</div>
