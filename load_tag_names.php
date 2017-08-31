<?php 
session_start();
if(!$_SESSION["logged_in"])	{
	echo "Please login </br>";
	header("location:login.php");
}
include "connectDb.php";

$text=htmlspecialchars(stripslashes(trim($_GET["text"])));
if(!empty($text))	{
	try	{
		$sql_tag="select tag_id,tag_name from tags where tag_name like '".$text."%'";
		foreach($conn->query($sql_tag) as $row_tag)	{
			$tag_name=$row_tag["tag_name"];
			$tag_id=$row_tag["tag_id"];
			if(!empty($tag_name))	{
				?>
			<a href="javascript:void(0)" id="tag-area-<?php echo $tag_id; ?>" onclick="populateText('<?php echo $tag_name; ?>')" ><?php echo $tag_name; ?></a></br>
			<?php
			}
			else
				echo 'Sorry! No such tags available';
		}
	}
	catch(PDOException $e)	{
		echo $e->getMessage();	
	}
}
?>
 
 