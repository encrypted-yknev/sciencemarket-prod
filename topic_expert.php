<?php
session_start();
include "connectDb.php";

if ($_SERVER["REQUEST_METHOD"] == "GET")	{
	$topic_id=htmlspecialchars(trim($_REQUEST['topic_id']));
	if(empty($topic_id))	{
		$message="Please choose a topic";
	}
	else	{
		try	{
			$sql_call_sp_experts = "call fetch_experts(".$topic_id.",@out_err_code,@out_err_desc)";
			$stmt_call_sp_experts=$conn->query($sql_call_sp_experts);
			$experts_row=$stmt_call_sp_experts->fetchAll();
			$stmt_call_sp_experts->closeCursor();
			
			$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
			if($result_query)	{
				$error_code=$result_query['error_code'];
				$error_desc=$result_query['error_desc'];
			}
			
			if(!strcmp($error_code,'00000'))	{
				$flag=1;
				$message="Experts fetched";		
			}
			else	{
				$message="Some error occurred";
			}
		}
		catch(PDOException $e)	{
			$message="Internal Server error ";
		}
	}
}
if($flag==1)	{
	$records=count($experts_row);
	$counter=0;
	while($counter < $records)	{
		$user_name = $experts_row[$counter]['user_id'];
		$user_img = $experts_row[$counter]['pro_img_url'];
		$disp_nm = $experts_row[$counter]['disp_name'];
		$shrt_bio = $experts_row[$counter]['shrt_bio'];
		$user_up_votes = $experts_row[$counter]['total_upvotes'];
		$user_down_votes = $experts_row[$counter]['total_downvotes'];
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
		
	?>
	</div><br/>
	<?php 
	$counter+=1;
	}
}	
?>
