<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";


function get_time_diff($timestamp_ans)	{
	date_default_timezone_set("Asia/Kolkata");
	$timestamp_cur=date("Y-m-d H:i:sa");
	
	$year1=substr($timestamp_ans,0,4);
	$month1=substr($timestamp_ans,5,2);
	$day1=substr($timestamp_ans,8,2);
	$hr1=substr($timestamp_ans,11,2);
	$min1=substr($timestamp_ans,14,2);
	$sec1=substr($timestamp_ans,17,2);


	$year2=substr($timestamp_cur,0,4);
	$month2=substr($timestamp_cur,5,2);
	$day2=substr($timestamp_cur,8,2);
	$hr2=substr($timestamp_cur,11,2);
	$min2=substr($timestamp_cur,14,2);
	$sec2=substr($timestamp_cur,17,2);

	if($year1 == $year2)	{
		if($month1 == $month2)	{
			if($day1 == $day2)	{
				if($hr1 == $hr2)	{
					if($min1 == $min2)	{
						if($sec1 == $sec2)	{
							$value=0;	
							$string="seconds";
						}
						else{
							$diff_sec=(int)$sec2-(int)$sec1;
							$value=$diff_sec;	
							$string="seconds";
						}
					}
					else{
						$diff_min=(int)$min2-(int)$min1;
						$value=$diff_min;
						$string="minutes";
					}
				}
				else{
					$diff_hr=(int)$hr2-(int)$hr1;
					$value=$diff_hr;
					$string="hours";
				}
			}
			else	{
				$diff_day=(int)$day2-(int)$day1;
				$value=$diff_day;
				$string="days";
			}
		}
		else	{
			$diff_mon=(int)$month2-(int)$month1;
			$value=$diff_mon;
			$string="months";
		}
	}
	if($value==1)
		$string=substr($string,0,strlen($string)-1);
	return $value.' '.$string.' ago';
}

try	{
	$sql_fetch_count1="select count(1) as cnt_following from followers where user_id = '".$_SESSION['user']."'";
	$sql_fetch_count2="select count(1) as cnt_followers from followers where following_user_id = '".$_SESSION['user']."'";
	
	$stmt_1=$conn->prepare($sql_fetch_count1);
	$stmt_1->execute();
	$result_1=$stmt_1->fetch();
	$count_1=$result_1['cnt_following'];

	$stmt_2=$conn->prepare($sql_fetch_count2);
	$stmt_2->execute();
	$result_2=$stmt_2->fetch();
	$count_2=$result_2['cnt_followers'];
}

catch(PDOException	$e)	{
	echo '';
}
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
			<div class="col-sm-2" id="main-side-column" style="background:#FBFBFB;">
			
				<div class="row">
					<div class="col-sm-7">
						<img src="<?php echo $_SESSION["pro_img"]; ?>" class="img-thumbnail img-responsive" alt="profile image" width="204" height="150"> 
					</div>
				</div></br>
				<div class="row">
					<div class="col-sm-8">
						<div class="side-menu-links">Following</div>
						<div class="side-menu-links">Followers</div>
					</div>
					<div class="col-sm-4">
						<div class="side-menu-links"><?php echo $count_1; ?></div>
						<div class="side-menu-links"><?php echo $count_2; ?></div>
					</div>
				</div>
			</div>
			<div class="col-sm-10" id="main-expert-section">
				<table border="0">
				<?php
					try	{
						$counter=1;
						$sql_fetch_users = "select user_id,up_votes,down_votes,pro_img_url from users where user_id <> '".$_SESSION['user']."' order by 2 desc,3 asc";
						foreach($conn->query($sql_fetch_users) as $row_users)	{
							$user_name = $row_users['user_id'];
							$user_up_votes = $row_users['up_votes'];
							$user_down_votes = $row_users['down_votes'];
							$user_img = $row_users['pro_img_url'];
							echo "<tr>";
							echo "<td class='td-col-0'><div class='user-image' style='background-image:url(\"".$user_img."\"); background-size:cover;' ></div></td>";
							echo "<td class='td-col-1'><strong><a href='profile.php?user=".$user_name."'>".$user_name."</a></strong></td>";
							echo "<td class='td-col-2'><span class='label label-success'>".$user_up_votes."</span>&nbsp;<span class='label label-danger'>".$user_down_votes."</span></td>";
							try	{
								$sql_check_follower = "select count(1) as count from followers where user_id='".$_SESSION['user']."' and following_user_id='".$user_name."'";
								$stmt_check_follower = $conn->prepare($sql_check_follower);
								$stmt_check_follower->execute();
								$row_user_count = $stmt_check_follower->fetch();
								$count_follower = $row_user_count['count'];
								if($count_follower > 0)	{
									$follow_class="btn btn-primary disabled btn-disabled";
									$unfollow_class="btn btn-danger";
									$is_follower=1;
									$click_attr_fol="";
									$click_attr_unfol="onclick='updateFollower(\"".$user_name."\",1,".$counter.")'";
								}
								else	{
									$follow_class="btn btn-primary";
									$unfollow_class="btn btn-danger disabled btn-disabled";
									$is_follower=0;	
									$click_attr_fol="onclick='updateFollower(\"".$user_name."\",0,".$counter.")'";
									$click_attr_unfol="";
								}
							}
							catch(PDOException $e)	{
								
							}
							echo "<td class='td-col-3'>
							<button id='follow-".$counter."' type='button' class='".$follow_class."' ".$click_attr_fol.">Follow</button>&emsp;
							<button id='unfollow-".$counter."'  type='button' class='".$unfollow_class."' ".$click_attr_unfol.">Unfollow</button></td>";
							echo "<td class='td-col-4' id='user-follow-".$counter."'></td>";
							echo "</tr>";
							$counter+=1;
						}
					}
					catch(PDOException $e)	{
						echo "Unable to fetch users list";
					}
				?>
				</table>
			</div>
		</div>
	</div>
</body>
</html>