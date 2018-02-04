<?php
session_start();
include "connectDb.php";
$final_interest=array();
$result=array();
$message="";
$flag=0;
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$interest_list=htmlspecialchars(trim($_REQUEST['int-val']));
	if(empty($interest_list))	{
		$message="Please choose at-least 4 tags";
	}
	else	{
		try	{
			$i=0;
			$sql_call_sp_interests = "call add_user_interests('".$interest_list."','".$_SESSION['user']."',@out_err_code,@out_err_desc)";
			$stmt_call_sp_interests=$conn->query($sql_call_sp_interests);
		#	$stmt_call_sp_interests->execute();
			$rowset=$stmt_call_sp_interests->fetchAll(PDO::FETCH_COLUMN,0);
			$final_interest=$rowset;
			$stmt_call_sp_interests->closeCursor();
			
			$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
			if($result_query)	{
				$error_code=$result_query['error_code'];
				$error_desc=$result_query['error_desc'];
			}
			
			if(!strcmp($error_code,'00000'))	{
				$_SESSION['interest']=$interest_list;
				$_SESSION['interest_list']=explode($interest_list,",");
				$flag=1;
				$message="Interests updated successfully";				
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
$result['suc_chk']=$flag;
$result['msg']=$message;
$result['int_list']=$final_interest;

echo json_encode($result); 
