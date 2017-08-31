<?php
/* session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
} */

include "connectDb.php";
function get_color()	{
	$rand_num = rand(1,10);
	switch($rand_num)	{
		case 1: return "#3498DB";
		case 2: return "#E74C3C";
		case 3: return "#17A589	";
		case 4: return "#58D68D";
		case 5: return "#138D75";
		case 6: return "#D4AC0D";
		case 7: return "#BA4A00";
		case 8: return "#E67E22";
		case 9: return "#8E44AD";
		case 10: return "#2E4053";
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

<div class="container" id="header-container">
	<div class="row">
		<div class="col-sm-2">
			<img src="<?php echo $slashes; ?>img/logo.jpg" width="200" height="50"/>
		</div>
		<div class="col-sm-6" id="header-menu">
			<!--<div id="h-nav">-->
				<div id="head-main-menu">
					<a href="<?php echo $slashes; ?>dashboard.php" class="header-navigation" id="dashboard-link"><span class="glyphicon glyphicon-home" style="margin-right:2px;"></span>Dashboard</a>
					<a href="<?php echo $slashes; ?>forum" class="header-navigation" id="forum-link"><span class="glyphicon glyphicon-question-sign" style="margin-right:2px;"></span>Q/A Forum</a>
					<a href="<?php echo $slashes; ?>expert_connect.php" class="header-navigation" id="connect-link"><span class="glyphicon glyphicon-user" style="margin-right:2px;"></span>Expert Connect</a>
					<a href="" class="header-navigation" id="collab-link"><span class="glyphicon glyphicon-refresh" style="margin-right:2px;"></span>Collaborate</a>
					<a href="" class="header-navigation" id="favours-link"><span class="glyphicon glyphicon-gift" style="margin-right:2px;"></span>Favours</a>
				</div>
				<div id="media-head-menu">
					<a href="<?php echo $slashes; ?>dashboard.php" class="header-navigation" id="dashboard-link"><span class="glyphicon glyphicon-home" style="margin-right:2px;"></span>Dashboard</a>
					<a href="<?php echo $slashes; ?>forum" class="header-navigation" id="forum-link"><span class="glyphicon glyphicon-question-sign" style="margin-right:2px;"></span>Q/A Forum</a>
					<a href="javascript:void(0)" class="header-navigation" id="more-link">+ More...</a>
					<ul id="head-nav-menu">
						<a href="<?php echo $slashes; ?>expert_connect.php" class="li-menu-link" ><li class="list-nav-menu">Expert Connect<span class="glyphicon glyphicon-transfer head-glyph" style="margin-left:4px;"></span></li></a>
						<a href="" class="li-menu-link" ><li class="list-nav-menu">Collaborate<span class="glyphicon glyphicon-refresh head-glyph" style="margin-left:4px;"></span></li></a>
						<a href="" class="li-menu-link" ><li class="list-nav-menu">Favours<span class="glyphicon glyphicon-gift head-glyph" style="margin-left:4px;"></span></li></a>
						<a href="<?php echo $slashes; ?>profile.php" class="li-menu-link" id="profile-link"><li class="list-nav-menu">My Profile<span class="glyphicon glyphicon-user head-glyph" style="margin-left:4px;"></span></li></a>
						<a href="<?php echo $slashes; ?>logout.php" class="li-menu-link" id="logout-link"><li class="list-nav-menu">Logout<span class="glyphicon glyphicon-off head-glyph" style="margin-left:4px;"></span></li></a>
					</ul>
				</div>
			
		</div>
		<div class="col-sm-4" id="qstn-nav">
			<span id="ask-lay"><a id="ask-link" href="<?php echo $slashes; ?>qstn.php">Ask Question</a></span>
			<span id="search-lay">
				<i class="glyphicon glyphicon-search"></i>
				<input id="srch-box" type="text" placeholder="Search question" onkeypress="fetchQuestions(this.value,'<?php echo $slashes ?>')"/>

			</span>	
			<div id="user-icon-section" style="background-color:<?php echo get_color(); ?>;">
				<?php 
					echo strtoupper(substr($_SESSION['name'],0,1)); 
				?>
			</div>
		</div>
		<ul id="profile-section">
			<a class="li-menu-link" href="<?php echo $slashes; ?>profile.php"><li class="list-nav-menu" id="">My Account<span class="glyphicon glyphicon-user head-glyph" style="margin-left:4px;"></span></li></a>
			<a class="li-menu-link" href="<?php echo $slashes; ?>logout.php"><li class="list-nav-menu" id="">Logout<span class="glyphicon glyphicon-off head-glyph" style="margin-left:4px;"></span></li></a>
		</ul>
	</div>
	<div id="srch-result">
		<span>No results</span>
	</div>
</div>
