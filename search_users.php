<?php
session_start();
if(!isset($_SESSION['logged_in']))	{
	header("location:index.php");
}

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
$userid=$slashes="";

if(isset($_REQUEST['userText']))
	$userid=trim(htmlspecialchars($_REQUEST['userText']));


	try	{
		if(empty($userid))
			$sql_fetch_users = "select distinct sender_id from messages where recipient_id='".$_SESSION['user']."'";
		else
			$sql_fetch_users = "select distinct user_id as sender_id from users where user_id like '".$userid."%'";
		$stmt_fetch_user=$conn->prepare($sql_fetch_users);
		$stmt_fetch_user->execute();
		$count = 0;
		if($stmt_fetch_user->rowCount() > 0)	{
			while($row_users = $stmt_fetch_user->fetch())	{
				$msg_sender = $row_users['sender_id'];
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
			echo "No users found";
		}
	}
	catch(PDOException $e)	{
		
	}
?>
				
