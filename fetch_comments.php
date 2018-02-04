<?php 
session_start();
if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;
include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
function convert_utc_to_local($utc_timestamp)	{
	$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
	$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
	$date_final = $date_utc->format('Y-m-d H:i:s');
	return $date_final;
}
$ans_list="";
$slashes="";
if(isset($_REQUEST['flag']))	{
	$token=$_REQUEST['flag'];
}
if(isset($_REQUEST['ansid']))	{
	$ansid=$_REQUEST['ansid'];
}
if(isset($_REQUEST['cid_list']))	{
	$cmnt_list=explode("|",$_REQUEST['cid_list']);
}
if(isset($_REQUEST['element_count']))	{
	$element_count=$_REQUEST['element_count'];
}


$comment_id_name = "";
if($token == 0)	{
	$comment_id_name="comment-list-recent-";
	$cmnt_card="rc";
}
else if($token == 1)	{
	$comment_id_name="comment-list-top-";
	$cmnt_card="tc";
}
else if($token == 2)	{
	$comment_id_name="comment-list-front-";
	$cmnt_card="fc";
}
$array_len=sizeof($cmnt_list);

if($element_count >= $array_len)	{
	echo 0;
	return;
}
$start_cmnt=$end_cmnt=0;
if($element_count < $array_len)  {
	$start_cmnt = $element_count;
}

if($start_cmnt+5 > $array_len)	{
	$end_cmnt = $array_len - 1;
}
else	{
	$end_cmnt = $start_cmnt+4;
}

try	{

	$sql="select comment_id,comment_desc,posted_by,created_ts 
		  from comments 
		  where ans_id=".$ansid." 
		  and comment_id between ".$cmnt_list[$start_cmnt]." and ".$cmnt_list[$end_cmnt]."
		  order by created_ts asc";
	$stmt=$conn->prepare($sql);
	$stmt->execute();
	if($stmt->rowCount() > 0)	{
		while($row_cmnt = $stmt->fetch())	{
			$comment_id=$row_cmnt['comment_id'];
			$comment=$row_cmnt['comment_desc'];
			$cmnt_posted_by=$row_cmnt['posted_by'];
			$created_ts = $row_cmnt['created_ts'];
			
			echo "<div class='user-comment-sec' id='comment-list-front-".$comment_id."'>".$comment." - <strong><span id='cmn-posted-".$comment_id."' onmouseleave='showUserCard(event,1,".$comment_id.",\"".$cmnt_card."\")' onmouseenter='showUserCard(event,0,".$comment_id.",\"".$cmnt_card."\")'><a href='profile.php?user=".$cmnt_posted_by."'>".$cmnt_posted_by."</a></span></strong>&nbsp;&nbsp;<span class='time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span></div>";
													
			$user_id_fetch=$cmnt_posted_by;
			
			include $slashes."fetch_user_dtls.php";
			$msg_div_id = "msg-fc-".$comment_id;
			$post_type="fc";
			$id=$comment_id;
			$user_card=$cmnt_posted_by;
			$up_vote=$up_user_votes;
			$down_vote=$down_user_votes;
			include $slashes."user_card.php"; 
			include $slashes."message_box.php";
		}
	}						
	
}
catch(PDOException $e)	{
	
}


	?>
