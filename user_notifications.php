<?php
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}

include "connectDb.php";

function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
}

function get_normal_time($db_time)	{
	$year = substr($db_time,0,4);
	$month = substr($db_time,5,2);
	$date = substr($db_time,8,2);
	
	$mth_str="";
	switch($month)	{
		case "01": $mth_str="Jan";
			break;
		case "02": $mth_str="Feb";
			break;
		case "03": $mth_str="Mar";
			break;
		case "04": $mth_str="Apr";
			break;
		case "05": $mth_str="May";
			break;
		case "06": $mth_str="Jun";
			break;
		case "07": $mth_str="Jul";
			break;
		case "08": $mth_str="Aug";
			break;
		case "09": $mth_str="Sep";
			break;
		case "10": $mth_str="Oct";
			break;
		case "11": $mth_str="Nov";
			break;
		case "12": $mth_str="Dec";
			break;
		default : $mth_str = "";
		break;
	}
	return $mth_str." ".$date.", ".$year;
}
?>
<html>
<head>
<title>Science Market - Notifications</title>
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
			<div id="page-title"><span>Notifications</span></div></br>
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
					<img id="propic" src="<?php echo $img_url; ?>" />
					<!--<a id="pic-link" href="upload.php"><span id="change-image-section">Change photo</span></a>-->
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
					<img id="propic" src="<?php echo $img_url; ?>" />
					<!--<a id="pic-link" href="upload.php"><span id="change-image-section"><span class="glyphicon glyphicon-camera"></span>&nbsp;&nbsp;Change photo</span></a>-->
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="profile.php">Profile Settings</a></li>
					<li class="active"><a href="user_notifications.php">Notifications</a></li>
					<li><a href="logout.php">Logout</a></li>
			    </ul>
			</div>
			<div class="col-sm-9" id="detl-section">
				<div id="profile-stats-section">
					<h3>Notifications</h3>
					<?php
						try	{
							$sql_fetch_notify="select notify_text,created_ts from notifications where user_id = '".$_SESSION['user']."' order by created_ts desc";
							
							$stmt_fetch_notify=$conn->prepare($sql_fetch_notify);
							$stmt_fetch_notify->execute();
							
							if($stmt_fetch_notify->rowCount() <= 0)	{
								echo "You don't have any notifications yet. Try asking questions, posting answers and comment on others answers so that you can get engaged and interact with other users.";
							}
							else	{
								echo "<table border='0'>";
								while($row_notify=$stmt_fetch_notify->fetch())	{
									echo '<tr><td class="td-col-1">'.get_normal_time($row_notify['created_ts']).'</td><td class="td-col-2">'.$row_notify['notify_text'].'</td></tr>';
								}
								echo "</table>";
							}
						}
						catch(PDOException $e)	{
							echo "Error fetching notifications. Please try again after some time.";
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
