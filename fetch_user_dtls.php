<?php
try	{
	$sql_fetch_votes="select * from users where user_id='".$user_id_fetch."'";
	foreach($conn->query($sql_fetch_votes) as $row_user)
		$img_url=$row_user["pro_img_url"];
		$up_user_votes=$row_user["up_votes"];
		$down_user_votes=$row_user["down_votes"];
		$disp_name = $row_user['disp_name'];
		$desc = $row_user['description'];
}
catch(PDOException	$e)	{
	
}
?>
