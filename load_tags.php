<?php
include "connectDb.php";
$tag_file=fopen("ip_file\\tags.txt","r") or die('Unable to open file');

while(!feof($tag_file))	{
	
	$str_tag=fgets($tag_file);
	$len=strlen($str_tag);
	$str_final=substr($str_tag,0,($len-3));

	 try		{
		$sql_insert_tag="insert into tags(tag_name) values('".$str_final."')";
		$stmt=$conn->prepare($sql_insert_tag);
		$stmt->execute();
		/* $row_count=$stmt->rowCount();
		if($row_count>0)
			echo "data inserted successfully!!";
		else
			echo "Some error occurred"; */
	}
	
	 catch(PDOException $e)	{
		echo "Internal server error";
	} 
}


fclose($tag_file);


?>