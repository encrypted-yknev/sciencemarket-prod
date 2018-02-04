<?php
session_start();
if(!isset($_SESSION['logged_in']))	{
	header("location:index.php");
}

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
$request_type="";
if(isset($_GET['type']))	
	$request_type=trim($_GET['type']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Science Market - Message inbox</title>
<meta name="description" content="Question and answer forum. Post questions. Answer questions. Discussion forums. clear your doubts. Portal for people to connect and discuss" >
<link rel="stylesheet" type="text/css" href="../styles/header.css">
<link rel="stylesheet" type="text/css" href="../styles/dashboard.css">
<link rel="stylesheet" type="text/css" href="../styles/qa_forum.css">
<link rel="stylesheet" type="text/css" href="../styles/footer.css">
<link rel="stylesheet" type="text/css" href="../styles/inbox.css">
<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type = "text/javascript" src = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/qa_forum.js"></script>
<script type="text/javascript" src="../js/inbox.js"></script>
<!--<script type="text/javascript" src="js/posts_vote.js"></script>-->
<script type="text/javascript" src="../js/header.js"></script></head>
<body onscroll="getHeight()">
<div id="block"></div>
<?php include "header.php"; ?>
	</br>
	<div class="container">
		<?php 
			include "forum/inbox_leftpane.php"; 
		?>
		<!--id="side-container"-->
						
			<div class="col-sm-3" id="right-section">
				<div id="srch-box">
					<input class="form-control" placeholder="search users" value="" onkeyup="searchUsers(this.value)" />
				</div>
				<div id="user-list">
					<?php
					try	{
						$sql_fetch_users = "select distinct t.user 
											from (select sender_id as user from messages where recipient_id='".$_SESSION['user']."'
										    union all 
											select recipient_id as user from messages where sender_id='".$_SESSION['user']."') as t
											";
						
						$stmt_fetch_user=$conn->prepare($sql_fetch_users);
						$stmt_fetch_user->execute();
						$count = 0;
						if($stmt_fetch_user->rowCount() > 0)	{
							while($row_users = $stmt_fetch_user->fetch())	{
								$msg_sender = $row_users['user'];
								$user_id_fetch=$msg_sender;
								include "fetch_user_dtls.php";
								?>
								<div class="user-row-section" id="user-nav-<?php echo $count; ?>" onclick="showMessage(<?php echo $count; ?>,'<?php echo $user_id_fetch; ?>')">
									<div class="usr-img" style="background-image:url('<?php echo $img_url; ?>');"></div>
									<div class="usr-text"><?php echo $msg_sender; ?></div>
								</div>
								
								<?php
								$count+=1;
							}
						}
						else	{
							
						}
					}
					catch(PDOException $e)	{
						
					}
					?>
				</div>
			</div>
			<div class="col-sm-6" >
				<div id="main-section">
					<div class="main-text">Click on user to view conversation</div>
				</div>
				</br>
				<input id="user-val" type="hidden" value="" />
				<div id="msg-box">
					<textarea id="msg-text" class="form-control" disabled="disabled" onkeypress="postMessageKeyPress(event,'<?php echo $_SESSION['user']; ?>')" placeholder="enter message"></textarea>
				</div>
			</div>
			<div class="col-sm-3" id="user-profile-section">
				
			</div>
		</div>
		</div>
	<?php include "footer.php"; ?>
</body>
</html>
