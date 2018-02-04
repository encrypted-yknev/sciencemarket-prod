<?php
session_start();
include "connectDb.php";

$success=false;
$upload_flag=1;
$update_flag=0;
$updt_db=false;

$pro_pic_success="<span style='color:#3c763d;'>Upload your image so that people can recognise you</span>";
$target_dir="uploads/";
	 
	if(isset($_FILES["propic"]))	{
		$file_name=$_FILES["propic"]["name"];									#Image file name
		$file_type=strtolower(pathinfo($target_dir.$file_name,PATHINFO_EXTENSION));			#type of image - jpg/jpeg/png etc.
		$file_size=$_FILES["propic"]["size"];									#image size
		$temp_file=$_FILES["propic"]["tmp_name"];								#temporary image name on server.
		$file_name_final=strtolower($target_dir.$_SESSION['user'].'.'.$file_type);
		
		if(strlen($file_name)==0)	{
			$pro_pic_success="Please choose an image to upload";
			$upload_flag=0;
		}
		else if($file_size == 0)	{
			$pro_pic_success="Image size exceeds the maximum allowed size";
			$upload_flag=0;
		}
		else if($file_type!="jpg" && $file_type!="jpeg" && $file_type!="png")	{						#check for file types.
			$pro_pic_success="Uh oh! Only jpg/jpeg/png images are allowed";
			$upload_flag=0;
		}
		else if($file_size > 800000)	{												#check for file size.
			$pro_pic_success="Sorry! Your file is too large";
			$upload_flag=0;
		}
		if($upload_flag==1)	{													#If all validations passed - upload the file.
		#	if(file_exists($file_name_final))	
		#		$update_flag=1;
			if(move_uploaded_file($temp_file,$file_name_final))
				$updt_db=true;
		}
		
		if($updt_db)	{														#update users table with image url.
			$_SESSION['pro_img']=$file_name_final;
			$success=true;
			$pro_pic_success="File uploaded successfully";
		}
	}
	else	{
		$pro_pic_success="File not sent to the server";
	}
	
	$return_data["textMsg"]=$pro_pic_success;
	$return_data["succ_cd"]=($success==true)?1:0;
	
	echo json_encode($return_data);
 ?>
