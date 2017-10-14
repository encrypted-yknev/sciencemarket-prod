<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:index.php");
}
include "connectDb.php";
if(isset($_GET['user']))	{
	$user_name=htmlspecialchars(stripslashes(trim($_GET['user'])));
	if($user_name == $_SESSION['user'])	{
		$user_name="";
		header("location:profile.php");
	}
	$sql_get_user_dtls="select disp_name,email_addr,ph_num,location,age,description,pro_img_url,up_votes,down_votes from users where user_id='".$user_name."'";
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
else
	$user_name="";

function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
}


?>
<html>
<head>
<title>Science Market - <?php echo strlen($user_name)==0?" User Dashboard" : " ".$user_disp_name." : Public profile"; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/profile.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
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
<!--<div id="block"></div>-->

	<?php
		$img_url="";
		try	{																			#fetch user details.
			$sql="select disp_name,email_addr,ph_num,location,age,description,pro_img_url,up_votes,down_votes from users where user_id='".$_SESSION["user"]."'";			
			$stmt=$conn->prepare($sql);
			$stmt->execute();
			$row=$stmt->fetch();
			$disp_name=$row["disp_name"];
			$email=$row["email_addr"];
			$mob=$row["ph_num"];
			$location=$row["location"];
			$age=$row["age"];
			$desc=$row["description"];
			$img_url=$row["pro_img_url"];
			$up_votes=$row["up_votes"];
			$down_votes=$row["down_votes"];
		}
		catch(PDOException $e)	{
			echo "Some error occured";
		}
	?>
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
						<div id="media-image"><img src="img/logo.jpg" width="200" height="50"/></div>
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
					<img id="propic" src="<?php echo strlen($user_name)==0? $img_url : $user_pro_img; ?>" />
					<?php 
					if(strlen($user_name)==0)	{
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
		<div class="row">
			<div class="col-sm-3" id="pro-section" > 
				<div id="proimg">
					<img id="propic" src="<?php echo strlen($user_name)==0? $img_url : $user_pro_img; ?>" />
					<?php 
					if(strlen($user_name)==0)	{
						?>
					<a id="pic-link" href="upload.php"><span id="change-image-section"><span class="glyphicon glyphicon-camera"></span>&nbsp;&nbsp;Change photo</span></a>
					<?php	} 	?>
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li class="<?php echo strlen($user_name)==0?'active':''?>"><a href="profile.php"><?php echo strlen($user_name)==0?"Profile Settings":"My Profile";?></a></li>
					<li><a href="user_notifications.php"><?php echo strlen($user_name)==0?"Notifications":"My Notifications";?></a></li>
					<li><a href="logout.php">Logout</a></li>
			    </ul>
			</div>
			<div class="col-sm-9" id="detl-section">
				
				<?php	
				if(strlen($user_name)==0)	{
					?>
				<div id="profile-stats-section">
					<h2>Welcome<?php echo ' '.get_first_name($disp_name); ?></h2>
					<h5><strong>Profile Stats</strong></h5>
					<div class="row">
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
							</ul>
						</div>
						<div class="col-sm-6">
						<div class="panel panel-default">
						  <div class="panel-body">You are following 10 people including A, B,...</div>
						  <div class="panel-body">20 people are following you including Q, W,...</div>
						</div>
						</div>
					</div>
				</div>
				<div class="row" id="row-4">
					<div class="col-sm-6 col-1">
						<h5 class="header-group"><span class="glyphicon glyphicon-pencil profile-edit"></span><strong>Edit Profile</strong></h5>
						Display Name: <input class="form-control" id="name" type="text" placeholder="" value="<?php echo $disp_name; ?>" onfocus="showTip(1)"/></br>
						Email: <input class="form-control" id="mail" type="text" placeholder="" value="<?php echo $email; ?>" onfocus="showTip(2)"/></br>
						Mobile: <input class="form-control" id="mob" type="text" placeholder="" value="<?php echo $mob; ?>" onfocus="showTip(3)"/></br>
						Location: <input class="form-control" id="location" type="text" placeholder="" value="<?php echo $location; ?>" onfocus="showTip(4)"/></br>
						About me: <textarea class="form-control" id="desc" rows="5" id="comment" onfocus="showTip(5)"><?php echo $desc; ?></textarea></br>
						<button type="button" class="btn btn-primary" onclick=
						"updateUser(document.getElementById('name').value,document.getElementById('mail').value,
						document.getElementById('mob').value,document.getElementById('location').value,
						document.getElementById('desc').value)">Save</button></br>
						<span id="message-section-1">
					</div>
					<div class="col-sm-6 col-2">
						<h5 class="header-group"><span class="glyphicon glyphicon-tags profile-edit"></span><strong>Update your interests</strong></h5></br>
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
									echo "<span>Your interests : ";
									
									while($row_interests = $stmt_check_interests->fetch())	{
										echo "<span class='badge disp-tags'>".$row_interests['tag_name']."</span>&nbsp;&nbsp;";
										$tags_html.="<span class='tag-name'>".$row_interests['tag_name']."</span>";
									}
								}
							}
							catch(PDOException $e)	{
								echo "Some error occured. Please try again after some time.";
							}
						?>
						</br></br>
						<span><strong><a href="javascript:void(0)" onclick="$('#tag').toggle()">Click here</a></strong> to add/remove interests</span>
						<div id="tag" class="">
							<input class="q-tags" type="text" name="q_tags" placeholder="Add interests+ENTER" />&emsp;
							<button type="button" class="btn btn-primary" onclick="addInterests(getTagsName())">Update</button></br></br>
							<div id="tag-res">
								<?php
									echo $tags_html;
								?>
							</div></br>
							<span id="message-section-2">
						</div>
					</div>
				</div>
				<div class="row" id="row-2">
					<div class="col-sm-6 col-1">
						<h5 class="header-group"><span class="glyphicon glyphicon-lock profile-edit" ></span><strong>Reset Password</strong></h5>
						Existing Password: <input type="password" class="form-control" id="pwd"></br>
						New Password: <input type="password" class="form-control" id="new-pwd"></br>
						Confirm Password: <input type="password" class="form-control" id="conf-pwd"></br>
						<button type="button" class="btn btn-primary" onclick="resetPwd(document.getElementById('pwd').value,document.getElementById('new-pwd').value,document.getElementById('conf-pwd').value)">Save</button></br>
						<span id="message-section-3"></span>
					</div>
					<div class="col-sm-6 col-2">
						<h5 class="header-group"><span class="glyphicon glyphicon-ban-circle profile-edit"></span><strong>De-activate account</strong></h5>
						Enter account password: <input type="password" class="form-control" id="deacc-account-pwd"></br>
						<button type="button" class="btn btn-primary" id="button-4" onclick="deactivateAcc()">Go</button></br>
						<span id="message-section-4"></span>
					</div>
				</div>
				<?php	}	
				else	{
					?>
				<div id="pub-profile-stats-section">
					<h3><?php echo $user_disp_name; ?></h3>
					<h5><strong>Profile Stats</strong></h5>
					<div class="row">
						<div class="col-sm-6">
							<ul class="list-group">
							  <li class="list-group-item">Questions asked <span class="badge">
								<?php
									$sql_fetch_qstn_count = "select count(1) as qstn_cnt from questions where posted_by = '".$user_name."'";
									$stmt_qstn=$conn->prepare($sql_fetch_qstn_count);
									$stmt_qstn->execute();
									$res_qstn=$stmt_qstn->fetch();
									$question_count = $res_qstn['qstn_cnt'];
									echo $question_count;
								?>
							  </span></li>
							  <li class="list-group-item">Questions answered<span class="badge">
								<?php
									$sql_fetch_ans_count = "select count(1) as ans_cnt from answers where posted_by = '".$user_name."'";
									$stmt_ans=$conn->prepare($sql_fetch_ans_count);
									$stmt_ans->execute();
									$res_ans=$stmt_ans->fetch();
									$answer_count = $res_ans['ans_cnt'];
									echo $answer_count;
								?>
							  </span></li> 
							  <li class="list-group-item">Total upvotes gained<span class="badge"><?php echo $user_up_votes; ?></span></li> 
							  <li class="list-group-item">Total downvotes<span class="badge"><?php echo $user_down_votes;?></span></li> 
							</ul>
						</div>
						<div class="col-sm-6">
						<div class="panel panel-default">
						  <div class="panel-body">Followers - </div>
						  <div class="panel-body">Following - </div>
						</div>
						</div>
					</div>
				</div>	
					
				<?php	}	?>
			</div>		
		</div>
	</div>
	</br></br>
	

<?php
	include "footer.php";
?>
</body>
</html>
