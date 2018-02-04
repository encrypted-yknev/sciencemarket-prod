<?php
session_start();
include "connectDb.php";
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$qid=htmlspecialchars(trim($_REQUEST['qid']));
	$setFlag=htmlspecialchars(trim($_REQUEST['setFlag']));
	$result=array();
	try	{
		$sql_call_sp_bookmarks = "call updt_bookmarks('".$_SESSION['user']."',".$qid.",".$setFlag.",@out_err_code,@out_err_desc)";
		$stmt_call_sp_bookmarks=$conn->prepare($sql_call_sp_bookmarks);
		$stmt_call_sp_bookmarks->execute();
		$stmt_call_sp_bookmarks->closeCursor();
		$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
		if($result_query)	{
			$error_code=$result_query['error_code']; 
			$error_desc=$result_query['error_desc'];
		}
		
		if(!strcmp($error_code,'00000'))	{		
			$result['err_cd']=0;
			$result['err_desc']='Interests updated successfully';
		}
		else	{
			$result['err_cd']=1;
			$result['err_desc']='Some error occurred. Please try again';
		}
	}
	catch(PDOException $e)	{
		$result['err_cd']=1;
		$result['err_desc']='Internal Server error';
	}
}
echo json_encode($result); 
