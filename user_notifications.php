<?php
session_start();
if(!$_SESSION["logged_in"])	{
	header("location:index.php");
}
include "connectDb.php";
include "forum/functions/get_time.php";
function get_first_name($x)	{
	if(strpos($x," "))	
		return substr($x,0,strpos($x," "));
	else
		return $x;
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
<script>
	$(document).ready(function()	{
		$.ajax({
			type:"post",
			url:"notifications.php",
			data:
			{
				"slash":"",
				"notify_typ":"DISPLAY"
			},
			success:function(result)	{
				$("#profile-stats-section").html(result);
			}
		});
	});
</script>
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
			$sql="select disp_name,email_addr,ph_num,location,dob,description,pro_img_url,up_votes,down_votes from users where user_id='".$_SESSION["user"]."'";			
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
			echo "Some error occured ";
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
					<div class="img-rounded img-thumbnail" id="propic-div" style="background-image:url('<?php echo $img_url; ?>');"></div>
					<!--<a id="pic-link" href="upload.php"><span id="change-image-section"><span class="glyphicon glyphicon-camera"></span>&nbsp;&nbsp;Change photo</span></a>-->
				</div></br></br>
				<ul class="nav nav-pills nav-stacked">
					<li><a href="profile.php">Profile Settings</a></li>
					<li class="active"><a href="user_notifications.php">Notifications</a></li>
					<li><a href="logout.php">Logout</a></li>
			    </ul>
			</div>
			<div class="col-sm-9" id="detl-section">
				<h3>Notifications</h3>
				<div id="profile-stats-section">					
				</div>
				<ul class="pagination">
					<li><a href="#">1</a></li> 
					<li class="active"><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">5</a></li>
				</ul>
			</div>
		</div>		
	</div>
	</br></br>
	

<?php
	include "footer.php";
?>
</body>
</html>
