<?php
session_start();
include "connectDb.php";

$success=false;


$pro_pic_success="<span style='color:#3c763d;'>Upload your image so that people can recognise you</span>";
	 
	if(isset($_FILES["propic"]))	{
	  $img = $_FILES["propic"];
	  $file_name = $img["name"];
	  $temp_file=$img["tmp_name"];								#temporary image name on server.
	  if(strlen($file_name) > 0)	{
		  $file_size=$img["size"];
		  if($file_size>0 and $file_size<= 500000)	{		 
			  $type_num   = exif_imagetype($temp_file);
				if($type_num == 2 or $type_num == 3)		{		# 2 - JPEG. 3 - PNG
				  $client_id="5e17483fe3b66c1";								#client secret - 3e8654eda3070450acc535e13026dfd39865cf0e
				  $handle = fopen($temp_file, "r");
				  $data = fread($handle, filesize($temp_file));
				  $pvars   = array('image' => base64_encode($data));
				  $timeout = 30;
				  $curl = curl_init();
				#  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				  curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
				  curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
				  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
				  curl_setopt($curl, CURLOPT_POST, 1);
				  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				  curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
				  $out = curl_exec($curl);
				  curl_close ($curl);
				  $pms = json_decode($out,true);
				  #var_dump($pms);
				  $url=$pms['data']['link'];
				  if($url!="")	{
				   try	{
					   $_SESSION["pro_img"]=$url;
					   $sql_updt_user = "update users set pro_img_url = '".$url."' where user_id = '".$_SESSION['user']."'";
					   $stmt_updt_user=$conn->prepare($sql_updt_user);
					   $stmt_updt_user->execute();
					   if($stmt_updt_user->rowCount() > 0)	{
						   $success=true;
						   $pro_pic_success = "File uploaded successfully ".$type;
					   }
					   else	{
						   $pro_pic_success = "Image uploaded with some errors";
					   }
				   }
				   catch(PDOException $e)	{
					   $pro_pic_success = "Some error occurred";
				   }
				  }	
				  else	{
				   $pro_pic_success = "Internal server error. Try again later ";
				  }
			   }	 
			  else	{
				  $pro_pic_success = "File size exceeds. Max 500KB";
			  } 
		  }
		  else	{
			  $pro_pic_success = "Invalid image type. Only JPEG/PNG allowed";
		  }
	  }
	  else	{
		  $pro_pic_success = "Please select an image to upload";
	  }
	}
	else	{
	  $pro_pic_success="File not sent to the server";
	}
	
	$return_data["textMsg"]=$pro_pic_success;
	$return_data["succ_cd"]=($success==true)?1:0;
	
	echo json_encode($return_data);
	
/* 	{
	  "data": {
		"id": "orunSTu",
		"title": null,
		"description": null,
		"datetime": 1495556889,
		"type": "image/gif",
		"animated": false,
		"width": 1,
		"height": 1,
		"size": 42,
		"views": 0,
		"bandwidth": 0,
		"vote": null,
		"favorite": false,
		"nsfw": null,
		"section": null,
		"account_url": null,
		"account_id": 0,
		"is_ad": false,
		"in_most_viral": false,
		"tags": [],
		"ad_type": 0,
		"ad_url": "",
		"in_gallery": false,
		"deletehash": "x70po4w7BVvSUzZ",
		"name": "",
		"link": "http://i.imgur.com/orunSTu.gif"
	  },
	  "success": true,
	  "status": 200
	}
 */ ?>
