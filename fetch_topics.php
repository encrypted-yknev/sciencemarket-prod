<?php

$message="";
$flag=0;
$topic_list=array();
try	{
	$sql_call_sp_topics = "call fetch_topics(".$topic_id_inp.",@out_err_code,@out_err_desc)";
	$stmt_call_sp_topics=$conn->query($sql_call_sp_topics);
	$topic_list=$stmt_call_sp_topics->fetchAll();
	$stmt_call_sp_topics->closeCursor();
	$result_query = $conn->query("select @out_err_code as error_code,@out_err_desc as error_desc")->fetch();
	if($result_query)	{
		$error_code=$result_query['error_code'];
		$error_desc=$result_query['error_desc'];
	}
	
	if(!strcmp($error_code,'00000'))	{
		$flag=1;
		$message="Details fetched";				
	}
	else	{
		echo "Some error occurred";
	}
}
catch(PDOException $e)	{
	echo "Internal Server error ";
}

?>

