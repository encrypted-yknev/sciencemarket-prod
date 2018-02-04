<?php
session_start();
if(!isset($_SESSION['logged_in']))	{
	header("location:index.php");
}

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
function convert_utc_to_local($utc_timestamp)	{
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		
	}
}
$slashes="";
if(isset($_REQUEST['userid']))	
	$userid=trim($_REQUEST['userid']);
?>
	<?php
		try	{
			$sql_fetch_messages="select * from messages where recipient_id = '".$_SESSION['user']."' and sender_id='".$userid."' 
								 union all
								 select * from messages where recipient_id = '".$userid."' and sender_id='".$_SESSION['user']."' order by created_ts";
			$stmt_fetch_messages=$conn->prepare($sql_fetch_messages);
			$stmt_fetch_messages->execute();
			
			if($stmt_fetch_messages->rowCount() > 0)	{
				
				while($row_msg=$stmt_fetch_messages->fetch())	{
					$user_id_fetch=$row_msg['sender_id'];
					include "fetch_user_dtls.php";
					if($user_id_fetch==$_SESSION['user'])
						$class_name="self-message";
					else
						$class_name="user-message";
	?>				
				<div class="<?php echo $class_name; ?>">	
					<div class="msg-row">
						<!--
						<div class="user-pic-section" style="background-image:url('<?php #echo $img_url; ?>'); background-size:cover;"></div>
						<div class="author-section">
							<div class="user-section"><strong><?php #echo $user_id_fetch;?></strong></div>
						</div></br></br>
						-->
						<div class="msg-section">
							<?php echo $row_msg['msg_text']; ?>						
							<span class="time-section"><?php echo ' - '.get_user_date(convert_utc_to_local($row_msg['created_ts']));?></span>
						</div>
					</div>
				</div>
				</br></br>
	<?php
				}
				
			}
			else	{
				echo "<div class='main-text'>No messages yet.Start conversation.</div>";
			}
		}
		catch(PDOException $e)	{
			
		}
	
	?>				
