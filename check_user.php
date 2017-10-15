<?php

include "connectDb.php";	
$user=addslashes(trim($_REQUEST['user']));

if(strlen($user) > 0)	{
	try		{
		$sql_check_unique_user="select count(1) as count_user from users where user_id='".$user."'";
		$stmt=$conn->prepare($sql_check_unique_user);
		$stmt->execute();
		$result=$stmt->fetch();
		$count=$result['count_user'];
		
		if($count > 0)
			echo "1";
		else
			echo "0";
	}

	catch(PDOException $e)	{
		echo "Internal server error";
	}
}
else
	echo "Enter Username";
?>
