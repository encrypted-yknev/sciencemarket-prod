<?php
try	{
	$sql_fetch_user="call fetch_user_dtls('".$user_id_fetch."',@out_err_cd,@out_err_desc)";
	$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
	if($result_query)	{
		$error_code=$result_query['error_code'];
		$error_desc=$result_query['error_desc'];
	}	
	if(!strcmp($error_code,'00000'))	{
		$stmt_fetch_user=$conn->query($sql_fetch_user);
		$stmt_fetch_user->setFetchMode(PDO::FETCH_ASSOC);
		while($row_user=$stmt_fetch_user->fetch())	{
			$email_addr=$row_user["email_addr"];
			$loc=$row_user["location"];
			$shrt_bio=$row_user["shrt_bio"];
			$ph_num=$row_user["ph_num"];
			$img_url=$row_user["pro_img_url"];
			$up_user_votes=$row_user["up_votes"];
			$down_user_votes=$row_user["down_votes"];
			$disp_name = $row_user['disp_name'];
			$desc = $row_user['description'];
		}
	}
	else	{
		echo "Error fetching user details";
	}
}
catch(PDOException $e)	{
	echo "Error fetching user details";
}
?>
