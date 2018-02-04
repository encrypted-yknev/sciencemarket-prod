<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Science Market - Expert Connect. Connect with your peers.</title>
<meta name="description" content="Science market. Connect with experts." >
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/header.css">
<link rel="stylesheet" type="text/css" href="styles/expert_connect.css">
<link rel="stylesheet" type="text/css" href="styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="styles/footer.css">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/expert_connect.js"></script>
<script type="text/javascript" src="js/header.js"></script></head>
<body onload="refreshNotify()">
<div id="block"></div>
<?php include "header.php"; ?>
	
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
			<div id="page-title"><span>Expert Connect</span></div></br>
			<div class="row">
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
			<div class="col-sm-3" id="main-side-column" style="background:#FBFBFB;">
			
				<h5 class="">Filter experts by topics</h5>
				<div id="topics-section">
					<?php
						$topic_id_inp=-1;
						include "fetch_topics.php";
						$i=0;
						echo "<select class='form-control' name='topic-list' id='topic-list'>";
						while($i < count($topic_list))	{
							echo "<option value='".$topic_list[$i]['topic_id']."'>".$topic_list[$i]['topic_desc']."</option>";
							$i+=1;
						}
						echo "</select>"
					?>
					</br>
					<button class="btn btn-primary expert-go" onclick="loadExperts()">Go</button></br>
				</div></br>
			</div>
			<div class="col-sm-9" id="main-expert-section">
				<?php
					try	{
						$counter=1;
						$sql_fetch_users = "select * from users where user_id <> '".$_SESSION['user']."' order by up_votes desc,down_votes asc";
						foreach($conn->query($sql_fetch_users) as $row_users)	{
							$user_name = $row_users['user_id'];
							$user_up_votes = $row_users['up_votes'];
							$user_down_votes = $row_users['down_votes'];
							$user_img = $row_users['pro_img_url'];
							$shrt_bio = $row_users['shrt_bio'];
							$disp_nm = $row_users['disp_name'];
				?>
							<div class="connect-row">
								<div class="user-image" style='background-image:url("<?php echo $user_img; ?>"); background-size:cover;'></div>
								<div class="user-txt">
									<div class="user-txt-1"><a href="profile.php?user=<?php echo $user_name; ?>"><?php echo '<strong>'.$disp_nm.'</strong> ('.$user_name.')'; ?></a></div>
									<?php if(!empty($shrt_bio))	{
										?>
									<div class="user-txt-2"><?php echo $shrt_bio; ?></div>
									<?php } ?>
									<div class="user-txt-3">
										<div class="user-sub-txt-1">Upvotes <?php echo $user_up_votes; ?>&emsp;</div>
										<div class="user-sub-txt-2">Downvotes <?php echo $user_down_votes; ?></div>
									</div><br/>
									<div class='user-txt-4' id='user-follow-<?php echo $counter; ?>'></div>
								</div>								
				<?php
								try	{
									$sql_check_follower = "select count(1) as count from followers where user_id='".$_SESSION['user']."' and following_user_id='".$user_name."'";
									$stmt_check_follower = $conn->prepare($sql_check_follower);
									$stmt_check_follower->execute();
									$row_user_count = $stmt_check_follower->fetch();
									$count_follower = $row_user_count['count'];
									if($count_follower > 0)	{
										$follow_class="btn btn-primary disabled btn-disabled btn-normal";
										$unfollow_class="btn btn-danger btn-normal";
										$is_follower=1;
										$click_attr_fol="";
										$click_attr_unfol="onclick='updateFollower(\"".$user_name."\",1,".$counter.")'";
									}
									else	{
										$follow_class="btn btn-primary btn-normal";
										$unfollow_class="btn btn-danger disabled btn-disabled btn-normal";
										$is_follower=0;	
										$click_attr_fol="onclick='updateFollower(\"".$user_name."\",0,".$counter.")'";
										$click_attr_unfol="";
									}
								}
								catch(PDOException $e)	{
									
								}
								echo "<div class='func-btn'>
								<button id='follow-".$counter."' type='button' class='".$follow_class."' ".$click_attr_fol.">Follow</button>&emsp;
								<button id='unfollow-".$counter."'  type='button' class='".$unfollow_class."' ".$click_attr_unfol.">Unfollow</button></div>";
								#echo "<div class='td-col-4' id='user-follow-".$counter."'></div>";
								$counter+=1
				?>
							</div><br/>
				<?php
						}
					}
					catch(PDOException $e)	{
						echo "Unable to fetch users list";
					}
				?>
			</div>
			<div class="col-sm-3"></div>
		</div>
	</div>
	<?php include "footer.php"; ?>
</body>
</html>
