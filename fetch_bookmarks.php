<?php
session_start();
include "connectDb.php";
if ($_SERVER["REQUEST_METHOD"] == "POST")	{
	$start_row=htmlspecialchars(trim($_REQUEST['start_row']));
	$record_count=htmlspecialchars(trim($_REQUEST['record_count']));
	$call_flag=htmlspecialchars(trim($_REQUEST['first_call']));
	$res_bk="";
	$result=array();
	try	{
		$sql_call_sp_bookmarks = "call fetch_bookmarks('".$_SESSION['user']."',".$start_row.",".$record_count.",".$call_flag.",@out_rcrd_count,@out_err_code,@out_err_desc)";
		$stmt_call_sp_bookmarks=$conn->query($sql_call_sp_bookmarks);
		$bk_list=$stmt_call_sp_bookmarks->fetchAll();
		$stmt_call_sp_bookmarks->closeCursor();
		$result_query = $conn->query("select @out_rcrd_count as rcrd_count,@out_err_code as error_code,@out_err_desc as error_desc")->fetch();
		if($result_query)	{
			$rcrd_count=$result_query['rcrd_count']; 
			$error_code=$result_query['error_code']; 
			$error_desc=$result_query['error_desc'];
		}
		
		if(!strcmp($error_code,'00000'))	{		
			$result['rcrd_count']=$rcrd_count;
			$result['err_cd']=0;
			$result['err_desc']=$error_desc;
			$i=0;
			$max_rec=($rcrd_count < $record_count)?$rcrd_count:$record_count;
			while($i < $max_rec){
				$qstn_id=$bk_list[$i]['post_id'];
				$qstn_title=$bk_list[$i]['qstn_titl'];
				$qstn_desc=$bk_list[$i]['qstn_desc'];
				
				$res_bk.= "<div class='qstn-row' id='bk-row-".$qstn_id."'>";
				$res_bk.= "<div class='remove-bk' id='bin-bk-".$qstn_id."' onclick='updtBookmarks(".$qstn_id.")'><img src='../img/svg/bin.svg' width='16' height='16'/></div>";
				$res_bk.= "<strong><a href='../qstn_ans.php?qid=".$qstn_id."' target='_blank'>".$qstn_title."</a></strong></br>";
				$res_bk.= $qstn_desc; 
				$res_bk.= "</div>";
				
				$i+=1;  
			}
			
			$result['res_bk']=$res_bk;
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
