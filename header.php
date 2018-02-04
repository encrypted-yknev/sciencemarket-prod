<?php
if(isset($_COOKIE['user_tz']))
	date_default_timezone_set($_COOKIE['user_tz']);

include "connectDb.php";

function convert_utc_to_local($utc_timestamp)	{
	
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		if(isset($_COOKIE['user_tz']))
			$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));	
		else
			$date_utc->setTimeZone(new DateTimeZone('UTC'));	
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		echo 'Some error occurred';
	}
}
$url_path = $_SERVER['PHP_SELF'];
$count_slash = substr_count($url_path,"/");
if($count_slash==1)
	$slashes = "";
else if($count_slash==2)
	$slashes = "../";
else if($count_slash==3)
	$slashes = "../../";
else if($count_slash==4)
	$slashes = "../../../";

?>

<header id="masthead" role="banner" class="">
	<div id="sc-logo-section">
		<div id="head-logo">
			<a href="<?php echo $slashes; ?>dashboard.php">
				<img src="<?php echo $slashes; ?>img/logo4.svg" width="60" height="60" />
				<img src="<?php echo $slashes; ?>img/logo.svg" width="180" height="55" />
			</a>
		</div>		
	</div>
	<div id="navigation-section">
		<nav id="menu1">
			<ul id="head-menu1">
				<a class="nav-link" href="<?php echo $slashes; ?>dashboard.php" title="User dashboard"><li class="nav-list">DASHBOARD</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>forum" title="Question answer forum"><li class="nav-list">FORUM</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>expert_connect.php" title="Connect with experts"><li class="nav-list">EXPERT CONNECT</li></a>
				<a class="nav-link-wait" href="#" title="Collaboration coming soon..."><li class="nav-list">COLLABORATE</li></a>
				<a class="nav-link-wait" href="#" title="Favours coming soon..."><li class="nav-list">FAVOURS</li></a>
			</ul>
		</nav>
	</div>
	<div id="navigation-section-media">
		<nav id="menu2">
			<ul id="head-menu2">
				<a class="nav-link" href="<?php echo $slashes; ?>dashboard.php" title="User dashboard"><li class="nav-list">DASHBOARD</li></a>
				<a class="nav-link" href="<?php echo $slashes; ?>forum"title="Question answer forum"><li class="nav-list">FORUM</li></a>
				<a class="nav-link" id="more-link" href="javascript:void(0)" ><li class="nav-list">MORE&nbsp;<div class="down-arrow"></div></li></a>
				<ul id="head-more-menu">
					<a class="sub-link" href="expert_connect.php" title="Connect with experts"><li class="sub-nav-list">EXPERT CONNECT</li></a>
					<a class="sub-link-wait" href="#"><li class="sub-nav-list" title="Collaboration coming soon...">COLLABORATE</li></a>
					<a class="sub-link-wait" href="#"><li class="sub-nav-list" title="Favours coming soon...">FAVOURS</li></a>
				</ul>
			</ul>
		</nav>
	</div>
	<div id="head-right-section">
	<?php if(isset($_SESSION['user']))	{
		?>
		<div id="head-user-pic" >
			<div id="user-pro" style="float:left;"></div>
			<div class="down-arrow" style="margin-left:5px;margin-top:15px;"></div>
			<ul id="head-pro-section">
				<a class="sub-link" href="<?php echo $slashes; ?>profile.php"><li class="sub-nav-list" id="">MY ACCOUNT</li></a>
				<a class="sub-link" href="<?php echo $slashes; ?>logout.php"><li class="sub-nav-list" id="">LOGOUT</li></a>
			</ul>
		</div>
	<?php	}
	else	{
	?>
	<span style="float:right;margin-top:15px;font-size:12px;">Hi <strong>Guest !</strong></span>
	<?php	}	?>
		<!--<div class="down-arrow" style="float:right;"></div>-->
		
		<div id="search-bar">
			<input class="form-control" type="text" placeholder="Search questions" onfocus='$("#block-bg").fadeIn()' onblur='$("#block-bg").fadeOut()' onkeyup="fetchQuestions(this.value,'<?php echo $slashes ?>')" />
		</div>		
		<div id="notify-sec">
			<a href="javascript:void(0)" id="not-btn" >
				<img src="<?php echo $slashes; ?>img/bell.svg" width="22" height="22" />
				<?php
					try	{
						$sql_notify_cnt = "select count(1) as not_cnt from notifications where user_id = '".$_SESSION['user']."' and view_flag = 'N'";
						$stmt_notify_cnt = $conn->prepare($sql_notify_cnt);
						$stmt_notify_cnt->execute();
						$row_cnt = $stmt_notify_cnt->fetch();
						$cnt_notify=$row_cnt['not_cnt'];
					}
					catch(PDOException $e)	{
						
					}
					if($cnt_notify > 0)	{
				?>
				<span class="badge" id="not-btn-val"><?php echo $cnt_notify; ?></span>
					<?php }	?>
			</a>
			<div id="notification-section">
			<!--	<div id="notify-caption"><strong>Notifications</strong></div></br>
				<div id="notify-header">
					<table>
						<th>
							<td id="notify-col1" class="table-notify-header" >
								<span id="notify-count-text"><strong><span id="notify-read-count"></span> unread notifcations</strong></span>
							</td>
							<td class="table-notify-header" >
								<button id="read-button" class="btn btn-primary" onclick="updateNotify(-1)">Mark all read</button>
							</td>
						</th>
					</table>
				</div></br>-->
				<div id="show-notify-section"></div>
			</div>
		</div>
		<div id="inbox-section"> 
			<a href="<?php echo $slashes; ?>inbox.php" id="msgtxt-btn" class="">
				<img src="<?php echo $slashes; ?>img/mail.svg" width="22" height="22" />
			</a>
		</div>
		<div id="qstn-ask">
			<a href="<?php echo $slashes; ?>qstn.php" class="btn btn-primary" target="_blank">ASK QUESTION</a>
		</div>
	</div>
</header>
<div id="srch-result">
	<span>No results</span>
</div>
<button id = "go-top-btn" class="btn btn-primary" onclick="window.scrollTo(0,0)">&#9650;</button>
<div id="block-bg"></div>
<input id="slash" type="hidden" value="<?php echo $slashes; ?>" />
