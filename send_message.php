<?php
session_start();
include "connectDb.php";

	$message="";

	$text_val = trim(htmlspecialchars(addslashes($_REQUEST['msg-text'])));
	$sender_id = $_REQUEST['sender_id'];
	$recp_id = $_REQUEST['recp_id'];
	
	if(!empty($text_val))	{
		try	{
			$sql_send_msg = "call send_message('".$sender_id."','".$recp_id."','".$text_val."',@out_err_cd,@out_err_desc)";
			$stmt_send_msg = $conn->prepare($sql_send_msg);
			$stmt_send_msg->execute();
			
			$row_sp = $conn->query("select @out_err_cd as error_code,@out_err_desc as error_desc")->fetch();
			$error_code=$row_sp['error_code'];
			$error_desc=$row_sp['error_desc'];
			if(!strcmp($error_code,'00000'))	{
				$message = "<div class='alert alert-success msg-profile'>Message sent successfully</div>";
			}
			else	{
				$message = "<div class='alert alert-danger msg-profile'>".$error_desc."</div>";
			}
		}
		catch(PDOException $e)	{
			echo $e->getMessage();
		}
	}
	else	{
		$message = "<div class='alert alert-warning msg-profile'>Uh Oh!! Please type some message</div>";
	}
	
	echo $message;

?>
