<?php
session_start();
if(!$_SESSION["logged_in"])	{
	$logged_in = 0;
}
else	{
	$logged_in = 1;
}
include "connectDb.php";
include "forum/functions/get_time.php";

if(isset($_GET['user']))	{
	$user_name=htmlspecialchars(stripslashes(trim($_GET['user'])));
	if($user_name == $_SESSION['user'])	{
		header("location:profile.php");
	}
	$sql_get_user_dtls="select * from users where user_id='".$user_name."'";
	$sql_get_follow_dtls="select count(1) count1 from followers where following_user_id='".$user_name."'";
	foreach($conn->query($sql_get_follow_dtls) as $res_fl1)
		$follow_cnt1 = $res_fl1['count1'];
	$sql_get_following_dtls="select count(1) count2 from followers where user_id='".$user_name."'";
	foreach($conn->query($sql_get_following_dtls) as $res_fl2)
		$follow_cnt2 = $res_fl2['count2'];
		
	$stmt_get_user_dtls=$conn->prepare($sql_get_user_dtls);
	$stmt_get_user_dtls->execute();
	if($stmt_get_user_dtls->rowCount() <= 0)
		header();
	else	{
		$result_user = $stmt_get_user_dtls->fetch();
		$user_disp_name=$result_user['disp_name'];
		$user_up_votes=$result_user['up_votes'];
		$user_down_votes=$result_user['down_votes'];
		$user_pro_img=$result_user['pro_img_url'];
		$user_desc=$result_user['description'];
	}
}
else	{
	$user_name="";
	$disp_name = $_SESSION["name"];
	$email=$_SESSION['mail'];
	$mob=$_SESSION['ph_num'];
	$location=$_SESSION['location'];
	$desc=$_SESSION['desc'];
	$img_url=$_SESSION["pro_img"];
	$up_votes=$_SESSION["up_votes"];
	$shrt_bio=$_SESSION['shrt_bio'];
	$dob=$_SESSION['dob'];
	$down_votes=$_SESSION["down_votes"];
}

function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
}


?>
<html>
<head>
<title>Science Market - <?php echo $user_name==""?" User Dashboard" : " ".$user_disp_name." : Public profile"; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/profile.css">
<link rel="stylesheet" type="text/css" href="styles/user_interest.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<script src="js/bootstrap.min.js"></script>
<script src="js/header.js"></script>
<script type="text/javascript" src="js/profile.js"></script>
</head>
<body>
<div id="block"></div>
<?php include "header.php"; ?>
	</br>
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
			<div id="page-title"><span>Profile</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-one">
						<a href="qstn.php" class="btn btn-info">Ask Questions</a>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-two">
						<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" />
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<div id="pro-section-media" > 
				<div id="proimg">
					<img id="propic" src="<?php echo $user_name==""? $img_url : $user_pro_img; ?>" />
					<?php 
					if($user_name=="")	{
						?>
					<a id="pic-link" href="upload.php"><span id="change-image-section">Change photo</span></a>
					<?php	
					}
					?>
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="profile.php" id="profile-link"><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
					<li><a href="dashboard.php" id="dashboard-link"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
					<li><a href="forum" id="forum-link"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
					<li><a href="" id="connect-link"><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
					<li><a href="" id="collab-link"><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
					<li><a href="" id="favours-link"><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
					<li><a href="logout.php" id="logout-link"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
				</ul>
			    
			</div></br>
		</div>
		<div id="upd-img-section">
			<h4>Choose your dashboard picture</h4>
			<div class="alert alert-success" id="success-msg">
				<?php 
					echo "Please upload your image"; 
				?>
			</div>
			<div class="progress" style="display:none">
			  <div class="progress-bar" role="progressbar" aria-valuenow="70"
			  aria-valuemin="0" aria-valuemax="100" style="width:0%">
				0%
			  </div>
			</div>
			<form id="imgupld-form" enctype="multipart/form-data">
				<input type="file" name="propic" id="propic"></br>
				<input type="submit" class="btn btn-primary" value="Upload" />&emsp;
				<input type="button" class="btn btn-danger" id="endupld" data-refresh="0" value="Close" />
			</form>
			</br></br>
		</div>
		<div class="row" id="edit-profile-section">
			<span class="x-win" onclick="hideEditWindow(1)">X</span>
			<h2 style="margin-left:10px;">Edit profile</h2>
			<div class="col-sm-6">
				<h5 class="header-group"><span class="glyphicon glyphicon-pencil profile-edit"></span><strong>Personal info</strong></h5>
				Username: <input class="form-control" id="user" type="text" onfocus="$('#profile-info-1').show();" onblur="$('#profile-info-1').hide();" placeholder="" 
				value="<?php echo stripslashes($_SESSION['user']); ?>" onfocusout="validateField(this.value,1)" />
				<div id="profile-info-1" class="profile-info">Enter unique user id</div></br>
				<div id="profile-edit-1"></div></br>
				Display Name: <input class="form-control" id="name" type="text" onfocus="$('#profile-info-2').show();" onblur="$('#profile-info-2').hide();" placeholder="" 
				value="<?php echo $disp_name; ?>"  onfocusout="validateField(this.value,2)"/>
				<div id="profile-info-2" class="profile-info">Enter proper name. Only alphabets and spaces allowed.</div></br>
				<div id="profile-edit-2"></div></br>
				Email: <input class="form-control" id="mail" type="text" onfocus="$('#profile-info-3').show();" onblur="$('#profile-info-3').hide();" placeholder="" 
				value="<?php echo $email; ?>" onfocusout="validateField(this.value,3)"/>
				<div id="profile-info-3" class="profile-info">Enter a valid e-mail of the format aaa@bbb.ccc</div></br>
				<div id="profile-edit-3"></div></br>
				Mobile: <input class="form-control" id="mob" type="text" onfocus="$('#profile-info-4').show();" onblur="$('#profile-info-4').hide();" placeholder="" 
				value="<?php echo $mob; ?>" onfocusout="validateField(this.value,4)"/>
				<div id="profile-info-4" class="profile-info">Enter valid contact number</div></br>
				<div id="profile-edit-4"></div></br>
				DOB: <input class="form-control" id="dob" type="date" onfocus="$('#profile-info-8').show();" onblur="$('#profile-info-8').hide();" placeholder="" 
				value="<?php echo $dob; ?>" />
				<div id="profile-info-8" class="profile-info">Select DOB from the drop down</div></br>
				<div id="profile-edit-8"></div></br>
				Location: <input class="form-control" id="location" type="text" onfocus="$('#profile-info-5').show();" onblur="$('#profile-info-5').hide();" placeholder="" 
				value="<?php echo $location; ?>" onfocusout="validateField(this.value,5)"/>
				<div id="profile-info-5" class="profile-info">Enter your residence</div></br>
				<div id="profile-edit-5"></div></br>
				Short bio: <input class="form-control" id="shrt_bio" type="text" onfocus="$('#profile-info-7').show();" onblur="$('#profile-info-7').hide();" value="<?php echo $shrt_bio; ?>" />
				<div id="profile-info-7" class="profile-info">Enter a Short bio/Profile headline. </div></br>
				<div id="profile-edit-7"></div></br>
				About me: <input class="form-control" id="desc" onfocus="$('#profile-info-6').show();" onblur="$('#profile-info-6').hide();" 
				value="<?php echo $desc; ?>" />
				<div id="profile-info-6" class="profile-info">Write something about yourself so that people can know you better</div></br>
				<div id="profile-edit-6"></div></br>
				<span id="message-section-1"></span></br>
				<button type="button" class="btn btn-primary" onclick="userUpdate(1)">Save</button></br></br>
			</div>
			<div class="col-sm-6">
				<div id="col-1">
					<h5 class="header-group"><span class="glyphicon glyphicon-lock profile-edit" ></span><strong>Reset Password</strong></h5>
					Existing Password: <input type="password" class="form-control" id="pwd"></br>
					New Password: <input type="password" class="form-control" id="new-pwd"></br>
					Confirm Password: <input type="password" class="form-control" id="conf-pwd"></br>
					<button type="button" class="btn btn-primary" onclick="userUpdate(3)">Save</button></br>
					<span id="message-section-3"></span>
				</div>			
				<div id="col-2">
					<h5 class="header-group"><span class="glyphicon glyphicon-ban-circle profile-edit"></span><strong>De-activate account</strong></h5>
					Enter account password: <input type="password" class="form-control" id="deacc-account-pwd"></br>
					<button type="button" class="btn btn-primary" id="button-4" onclick="userUpdate(4)">Go</button></br>
					<span id="message-section-4"></span>
				</div>
			</div>
		</div>
		<div id="edit-interest-section">
			<div id="bg-window"></div>
			<div id="main-container1">
				<span class="x-win" onclick="hideEditWindow(2)">X</span>
				<h2 style="margin-left:10px;">Edit interests</h2>
			<?php
				$tags_html="";
				try	{
					$sql_check_interests = "select b.tag_name 
											from tags b 
											inner join user_tags a 
											on b.tag_id = a.tag_id
											where a.user_id = '".$_SESSION['user']."'";
					$stmt_check_interests = $conn->prepare($sql_check_interests);
					$stmt_check_interests->execute();
					
					if($stmt_check_interests->rowCount() <= 0)	{
						echo "You haven't added your interests yet";
					}
					else	{
						echo "<strong><span style='margin-left:20px;font-size:12px;'>Your interests : </span></strong>";
						echo "<div id='user-interest-sec'>";
						while($row_interests = $stmt_check_interests->fetch())	{
							echo "<span class='badge disp-tags'>".$row_interests['tag_name']."</span>";
							$tags_html.="<span class='tag-name' data-source='user'>".$row_interests['tag_name']."</span>";
						}
						echo "</div>";
					}
				}
				catch(PDOException $e)	{
					echo "Some error occured. Please try again after some time.";
				}
			?>
			</br>
			<span style="margin-left:20px;"><strong><a href="javascript:void(0)" onclick="$('#tag').toggle()">Click here</a></strong> to add/remove custom interests</span>
			<div id="tag" class="">
				<input class="q-tags" type="text" name="q_tags" placeholder="Add interests+ENTER" />&emsp;
				<button type="button" class="btn btn-primary" onclick="addInterests(getTagsName())">Update</button></br></br>
				<div id="tag-res">
					<?php
						echo $tags_html;
					?>
				</div></br>
			</div>
			<div style="margin-left:20px;" id="message-section-2"></div>  
			<div id="mid-element">
				<div id="dot-class">
					.</br></br>
				</div>
				<span id="or-sec">OR</span>
				<div id="dot-class">
					.</br>
				</div>
			</div>
			<?php 
				$message="";
				$title="Add from below list of topics";
				include "user_interest_snippet.php"; 
			?>
			<div style="text-align:center;">
				<button class="btn btn-success" onclick="addInterests(getTagsName())">Add Interests</button>
			</div></br>
			</div>
		</div>
		<div class="row" id="main-profile-section">
			<div class="col-sm-3" id="pro-section" > 
				<div id="proimg">
					<div class="img-rounded img-thumbnail" id="propic-div" style="background-image:url('<?php echo strlen($user_name)==0? $img_url : $user_pro_img; ?>');"></div>
				<!--	<img id="propic" src="<?php #echo strlen($user_name)==0? $img_url : $user_pro_img; ?>" /> -->
					<?php 
					if(strlen($user_name)==0)	{
						?>
					<a id="pic-link" href="javascript:void(0)" onclick="showEditWindow(3)"><span id="change-image-section"><span class="glyphicon glyphicon-camera"></span>&nbsp;&nbsp;Change photo</span></a>
					<?php	} 	?>
				</div></br>
				<?php if(strlen($user_name)==0)	{ ?>
				</br>
				<ul class="nav nav-pills nav-stacked">
					<li class="active"><a href="profile.php">Profile Settings</a></li>
					<li><a href="user_notifications.php">Notifications</a></li>
					<li><a href="logout.php">Logout</a></li>
			    </ul>
				<?php }	else	{ ?>
				<div id="userid-text">userid - <?php echo $user_name; ?></div></br>
				
				<?php } ?>
			</div>
			<div class="col-sm-9" id="detl-section">
				
				<?php	
				if(strlen($user_name)==0)	{
					?>
				<div id="profile-stats-section">
					<h2>Welcome<?php echo ' '.get_first_name($disp_name); ?></h2>
					<div class="abt-section"><?php echo (trim($_SESSION["shrt_bio"])=="")?("Short bio is not updated"):stripslashes($_SESSION["shrt_bio"]); ?></div></br>
					<div class="row">
						<div class="col-sm-6">							
							<div class="detl-sec">
								<div id="user-val">
									<img src="img/svg/bio.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo (trim($_SESSION['desc'])=="")?"User hasn't updated his bio":$_SESSION['desc']; ?></span>									
									<span style="color:blue;font-size:13px;float:right;cursor:pointer;text-decoration:underline;" href="javascript:void(0)" onclick="showEditWindow(1)">Edit profile</span>
								</div>
								<div id="user-val">
									<img src="img/svg/user.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo $_SESSION['user']; ?></span>
								</div>
								<div id="mob-val">
									<img src="img/svg/mob.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo ($_SESSION['ph_num']==0)?"<em>Contact not added</em>":$_SESSION['ph_num']; ?></span>
								</div>
								<div id="mail-val">
									<img src="img/svg/mail.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo $_SESSION['mail']; ?></span>
								</div>
								<div id="locate-val">
									<img src="img/svg/locate.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo (trim($_SESSION['location'])=="")?"<em>Location not added</em>":$_SESSION['location']; ?></span>
								</div>
								<div id="interest-val">
									<img src="img/svg/interest.svg" width="18" height="18"/>
									<span style="margin-left:10px;"><?php echo $_SESSION['interest']; ?></span>
									<span style="color:blue;font-size:13px;float:right;cursor:pointer;text-decoration:underline;" href="javascript:void(0)" onclick="showEditWindow(2)">Edit interests</span>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<ul class="list-group">
							  <li class="list-group-item">Questions asked <span class="badge">
								<?php
									$sql_fetch_qstn_count = "select count(1) as qstn_cnt from questions where posted_by = '".$_SESSION['user']."'";
									$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
									$stmt_qstn->execute();
									$res_qstn=$stmt_qstn->fetch();
									$question_count = $res_qstn['qstn_cnt'];
									echo $question_count;
									
									
									
								?>
							  
							  </span></li>
							  <li class="list-group-item">Questions answered<span class="badge">
								<?php
									$sql_fetch_ans_count = "select count(1) as ans_cnt from answers where posted_by = '".$_SESSION['user']."'";
									$stmt_ans=$conn->prepare($sql_fetch_ans_count);
									$stmt_ans->execute();
									$res_ans=$stmt_ans->fetch();
									$answer_count = $res_ans['ans_cnt'];
									echo $answer_count;
								?>
							  </span></li> 
							  <li class="list-group-item">Total upvotes gained<span class="badge"><?php echo $up_votes; ?></span></li> 
							  <li class="list-group-item">Total downvotes<span class="badge"><?php echo $down_votes;?></span></li> 
							  <li class="list-group-item">Followers<span class="badge"><?php echo $_SESSION["flw_1"];?></span></li> 
							  <li class="list-group-item">Following<span class="badge"><?php echo $_SESSION["flw_2"]; ?></span></li> 
							</ul>
						</div>						
					</div>
				</div>								
				<?php	}	
				else	{
					?>
				<div id="pub-profile-stats-section">
					<h3><?php echo $user_disp_name; ?></h3>
					<h4 class="page-title-section">Profile</h4></br>
					<div class="row">
						<div class="col-sm-6">
							<div class="panel panel-default">
							  <div class="panel-body abt-section"><?php echo ($user_desc=="")?("User hasn't updated his bio"):$user_desc; ?></div>
							</div>
							<div class="interest-panel">
								<div id="user-interest-text">Areas of interests</div>
							<div class="panel-body" id="user-int-tags">
							<?php 
							try	{
								$sql_fetch_tags="select t1.tag_name from tags t1 inner join user_tags t2 on t1.tag_id=t2.tag_id where t2.user_id='".$user_name."'";
								$stmt_fetch_tags = $conn->prepare($sql_fetch_tags);
								$stmt_fetch_tags->execute();
								
								if($stmt_fetch_tags->rowCount() <= 0)	{
									echo "No interests added by added";
								}
								else	{
									while($row_tags = $stmt_fetch_tags->fetch())	{
										echo "<span class='badge disp-tags-user'>".$row_tags['tag_name']."</span>&nbsp;&nbsp;";							
									}
								}
							}
							catch(PDOException $e)	{
								
							}
							?>
							</div></div></br>
							<div class="btn-section">
								<button class="btn btn-primary" onclick="showMessageBox(0,'msg-profile')">Message</button>
								<?php 
									$msg_div_id="msg-profile";
									$user_card=$user_name;
									include "message_box.php"; 
								?>
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
											$click_attr_unfol="onclick='updateFollower(\"".$user_card."\",1,".$logged_in.")'";
										}
										else	{
											$follow_class="btn btn-primary";
											$unfollow_class="btn btn-danger disabled btn-disabled";
											$is_follower=0;	
											$click_attr_fol="onclick='updateFollower(\"".$user_card."\",0,".$logged_in.")'";
											$click_attr_unfol="";
										}
									}
									catch(PDOException $e)	{
										
									}
									?>									
								<button id="follow" class="<?php echo $follow_class; ?>"<?php echo$click_attr_fol; ?>>Follow</button>
								<button id="unfollow" class="<?php echo $unfollow_class; ?>" <?php echo$click_attr_unfol; ?>>Unfollow</button></br></br>
								<div id="follow-message"></div>
							</div>
						</div>
						<div class="col-sm-6">
						<div class="panel panel-default">
						  <ul class="list-group">
							  <li class="list-group-item">Questions asked <span class="badge stat-count">
								<?php
									$sql_fetch_qstn_count = "select count(1) as qstn_cnt from questions where posted_by = '".$user_name."'";
									$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
									$stmt_qstn->execute();
									$res_qstn=$stmt_qstn->fetch();
									$question_count = $res_qstn['qstn_cnt'];
									echo $question_count;
								?>
							  </span></li>
							  <li class="list-group-item">Questions answered<span class="badge stat-count">
								<?php
									$sql_fetch_ans_count = "select count(1) as ans_cnt from answers where posted_by = '".$user_name."'";
									$stmt_ans=$conn->prepare($sql_fetch_ans_count);
									$stmt_ans->execute();
									$res_ans=$stmt_ans->fetch();
									$answer_count = $res_ans['ans_cnt'];
									echo $answer_count;
								?>
							  </span></li> 
							  <li class="list-group-item ">Total upvotes gained<span class="badge stat-count"><?php echo $user_up_votes; ?></span></li> 
							  <li class="list-group-item">Total downvotes<span class="badge stat-count"><?php echo $user_down_votes;?></span></li> 
							  <li class="list-group-item">Followers<span class="badge stat-count"><?php echo $follow_cnt1;?></span></li> 
							  <li class="list-group-item ">Following<span class="badge stat-count"><?php echo $follow_cnt2;?></span></li> 
							</ul>
						</div>
						</div>
					</div>
				</div>	
				<?php	}	?>
				<div id="user-posts-section">
					<h4 class="page-title-section">Top posts</h4>
					<?php 
					try	{
						if($user_name=="")
							$user_by = $_SESSION['user'];
						else
							$user_by = $user_name;
						$sql_user_top_posts="select * from questions where posted_by='".$user_by."' order by up_votes desc,down_votes asc limit 10";
						$stmt_user_top_posts=$conn->prepare($sql_user_top_posts);
						$stmt_user_top_posts->execute();
						if($stmt_user_top_posts->rowCount() > 0)	{
							while($row_top_posts=$stmt_user_top_posts->fetch())	{
								$qstn_id=$row_top_posts['qstn_id'];
								$qstn_titl=$row_top_posts['qstn_titl'];
								$qstn_desc=$row_top_posts['qstn_desc'];
								$qstn_ts=get_user_date(convert_utc_to_local($row_top_posts['created_ts']));
								echo "<div class='user-qstn-row'>";
								echo "<h5><strong><a href='qstn_ans.php?qid=".$qstn_id."' target='_blank'>".$qstn_titl." </a></strong> - <span class='qstn-ts'>".$qstn_ts."</span></h5>";
								echo "<p>".$qstn_desc."</p>";
								echo "</div>";
								echo "</br>";
							}
						}
						else	{
							echo "<div style='text-align:center;'>".((strlen($user_name)==0)?"You haven't posted any questions yet":$user_name." hasn't posted any questions yet")."</div>";
						}
					}
					catch(PDOException $e)	{
						
					}
					?>
					</br>
					<h4 class="page-title-section">Recent posts</h4>
					<?php
					try	{
						$sql_user_recent_posts="select * from questions where posted_by='".$user_by."' order by created_ts desc limit 10";
						$stmt_user_recent_posts=$conn->prepare($sql_user_recent_posts);
						$stmt_user_recent_posts->execute();
						if($stmt_user_recent_posts->rowCount() > 0)	{
							while($row_recent_posts=$stmt_user_recent_posts->fetch())	{
								$qstn_id=$row_recent_posts['qstn_id'];
								$qstn_titl=$row_recent_posts['qstn_titl'];
								$qstn_desc=$row_recent_posts['qstn_desc'];
								$qstn_ts=get_user_date(convert_utc_to_local($row_recent_posts['created_ts']));
								echo "<div class='user-qstn-row'>";
								echo "<h5><strong><a href='qstn_ans.php?qid=".$qstn_id."' target='_blank'>".$qstn_titl." </a></strong> - <span class='qstn-ts'>".$qstn_ts."</span></h5>";
								echo "<p>".$qstn_desc."</p>";
								echo "</div>";
								echo "</br>";
							}
						}
						else	{
							echo "<div style='text-align:center;'>".(($user_name=="")?"You haven't posted any questions yet":$user_name." hasn't posted any questions yet")."</div>";
						}
					}
					catch(PDOException $e)	{
						
					}
					?>
				</div>												
			</div>		
		</div>
	</div>
	</br></br>
	

<?php
	include "footer.php";
?>
</body>
</html>
