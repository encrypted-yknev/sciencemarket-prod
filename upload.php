<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BioForum : Upload picture</title>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="styles/upload.css" rel="stylesheet">
<link href="styles/header.css" rel="stylesheet">
<link href="styles/footer.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/header.js"></script>
<link rel="stylesheet" href="styles/bootstrap.min.css">
<!-- Latest compiled JavaScript -->
</head>
<body>

<?php
	include "header.php";
	$upload_flag=1;
	$update_flag=0;
	$updt_db=false;
	$pro_pic_success="Upload your image so that people can recognise you.";
	$target_dir="uploads/";
	if(isset($_POST["submit"]))	{
		
		if(isset($_FILES["propic"]))	{
			$file_name=$_FILES["propic"]["name"];									#Image file name
			$file_type=pathinfo($target_dir.$file_name,PATHINFO_EXTENSION);			#type of image - jpg/jpeg/png etc.
			$file_size=$_FILES["propic"]["size"];									#image size
			$temp_file=$_FILES["propic"]["tmp_name"];								#temporary image name on server.
			$file_name_final=$target_dir.$_SESSION['user'].'.'.$file_type;
			
			if(strlen($file_name)==0)	{
				$pro_pic_success="Please choose an image to upload";
				$upload_flag=0;
			}

			else if(($file_type!="jpg" && $file_type!="JPG" ) && 
			   ($file_type!="jpeg" && $file_type!="JPEG"))	{						#check for file types.
				$pro_pic_success="Uh oh! Only jpg/jpeg images are allowed";
				$upload_flag=0;
			}
			else if($file_size > 800000)	{												#check for file size.
				$pro_pic_success="Sorry! Your file is too large";
				$upload_flag=0;
			}
			
			
			if($upload_flag==1)	{													#If all validations passed - upload the file.
				if(file_exists($file_name_final))	
					$update_flag=1;
				if(move_uploaded_file($temp_file,$file_name_final))
					$updt_db=true;
			}
			
			if($updt_db)	{														#update users table with image url.
				try	{
					$sql="update users set pro_img_url='".$file_name_final."' where user_id='".$_SESSION["user"]."'";
					#echo $sql;
					$stmt=$conn->prepare($sql);
					$stmt->execute();
				#	echo 'rowcount - '.$stmt->rowCount();
					if($stmt->rowCount()>0 or $update_flag=1)	{
						$_SESSION['pro_img']=$file_name_final;
						$pro_pic_success="File uploaded successfully";
					}
					else	{
						$pro_pic_success="Some error occured";
					}
				}
				catch(PDOException $e)	{
					$pro_pic_success="Error Occurred";
				}
			}
		}
		
		else	{
			$pro_pic_success="File not sent to the server";
		}
	}
 ?>
	
	</br>
	
	<div class="container">
		<!--<div class="row"><div class="col-sm-12"> </div></div></br></br>-->
		<div class="row">
			
			<div class="col-sm-4" id="left-column">
				<div class="row">
					<div class="col-sm-3"></div>
					<div class="col-sm-6">
						<h4>Preview</h4>
						<?php
							try {
								$sql_fetch_img="select pro_img_url from users where user_id='".$_SESSION['user']."'";
								$stmt=$conn->prepare($sql_fetch_img);
								$stmt->execute();
								$result=$stmt->fetch();
							}
							catch(PDOException $e)	{
								
							}
							?>
						<img id="preview-image" src="<?php echo $result['pro_img_url']; ?>" class="img-responsive" alt="profile image" width="150" height="200">
						
					</div>
					<div class="col-sm-3"></div>
				</div>
				
			</div>
			<div class="col-sm-4" id="mid-column">
				<h4>Choose your dashboard picture</h4>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
					<div class="alert alert-success" id="success-msg">
						<?php echo $pro_pic_success; ?>
					</div>
					</br>
					<input type="file" name="propic" id="propic"></br>
						
					<input type="submit" value="Upload" name="submit"></br></br>
				</form>
			</div>
			<div class="col-sm-4" id="right-column">
				<h4>Image criteria</h4>
				<div class="alert alert-info">
				  <li>Only jpeg/jpg/png images are supported.</li>
				  <li>Image size should not be greater than 500KB.</li>
				</div>
			</div>
		</div>
	</div>
 </body>
 </html>
 
 <?php #include "footer.php"; ?>
 
 