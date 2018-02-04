<?php 
session_start();

if(isset($_SESSION['logged_in']) and $_SESSION['logged_in'])
	$logged_in=1;
else
	$logged_in=0;

include "connectDb.php";
include "forum/functions/get_time.php";
include "forum/functions/get_time_offset.php";
function convert_utc_to_local($utc_timestamp)	{
	try	{
		$date_utc=new DateTime($utc_timestamp,new DateTimeZone('UTC'));
		$date_utc->setTimeZone(new DateTimeZone($_COOKIE['user_tz']));
		$date_final = $date_utc->format('Y-m-d H:i:s');
		return $date_final;
	}
	catch(Exception $e)	{
		
	}
}
$qstn_list="";
if(isset($_REQUEST['qstn_list']))	{
	$qstn_list=explode("|",$_REQUEST['qstn_list']);
}
if(isset($_REQUEST['root']))	{
	$slashes=$_REQUEST['root'];
}
if(isset($_REQUEST['questions']))	{
	$qstn_count=$_REQUEST['questions'];
}
$array_len=sizeof($qstn_list);

if($qstn_count >= $array_len)	{
	echo "0";
	return;
}
$start_qstn=$end_qstn=0;
if($qstn_count < $array_len)  {
	$start_qstn = $qstn_count;
}

if($qstn_count+10 > $array_len)	{
	$end_qstn = $array_len - 1;
}
else	{
	$end_qstn = $qstn_count+9;
}

while($start_qstn <= $end_qstn)	{
	$sql="select a.qstn_id,
				 a.qstn_titl,
				 a.qstn_desc,
				 a.posted_by,
				 a.topic_id,
				 a.up_votes,
				 a.down_votes,
				 a.created_ts 
		 from questions a
		 where a.qstn_id = ".$qstn_list[$start_qstn];
	$stmt=$conn->prepare($sql);
	$stmt->execute();
							
	if($stmt->rowCount() > 0)	{
		
		while($row = $stmt->fetch())	{
			$qid=$row['qstn_id'];
			$posted_by=$row['posted_by'];
			$created_ts=$row['created_ts'];
			$topic_id=$row['topic_id'];
			$up_votes=$row['up_votes'];
			$down_votes=$row['down_votes'];
				?>
				<div class="qstn_row">
				
				<div class="qstn-topic-section">
					<?php
						try	{
							$sql_fetch_topic = "select topic_desc,parent_topic from topics where topic_id = ".$topic_id;
							$stmt_fetch_topic=$conn->prepare($sql_fetch_topic);
							$stmt_fetch_topic->execute();
							$res_topic=$stmt_fetch_topic->fetch();
							
							$topic_desc=$res_topic['topic_desc'];
							$parent_topic=$res_topic['parent_topic'];
							
							$sql_fetch_parent_topic = "select topic_desc from topics where topic_id=".$parent_topic;
							$stmt_fetch_par_topic=$conn->prepare($sql_fetch_parent_topic);
							$stmt_fetch_par_topic->execute();
							$res_par_topic=$stmt_fetch_par_topic->fetch();
							
							$par_topic_desc = $res_par_topic['topic_desc'];
							echo $par_topic_desc." - ".$topic_desc;
						}
						catch(PDOException $e)	{
							
						}
						
						$user_id_fetch=$posted_by;
						
						include "fetch_user_dtls.php";
					
					?>
				</div>
				<div class="user-img-section" style="background-image:url('<?php echo $img_url; ?>'); background-size:cover;">
					
				</div>
				<div class="auth-section">
					<?php
						echo "<span id='qstn-posted-".$qid."' 
								onmouseleave='showUserCard(event,1,".$qid.",\"q\")' 
								onmouseenter='showUserCard(event,0,".$qid.",\"q\")'>
								<a href='".$slashes."profile.php?user=".$posted_by."'>".$posted_by."</a></span> -
								<span class='qstn-time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span>";
					?>
				</div></br>
				<?php 
					$post_type="q";
					$user_card=$posted_by;
					$up_vote=$up_user_votes;
					$down_vote=$down_user_votes;
					$id=$qid;
					include "user_card.php"; 
				?>
				</br>
				<a class="titl-link" href="<?php echo $slashes.'qstn_ans.php?qid='.$qid ?>"><?php echo $row["qstn_titl"]; ?></a>&emsp;
				<span id="qstn-ans-count"></span>
				<p id="qstn-desc"><?php echo $row["qstn_desc"]; ?></p>
		
				<div class="tag-section">
				<?php
					try	{
						$sql_fetch_qstn_tags="select a.tag_name 
											  from tags a,qstn_tags b 
											  where a.tag_id=b.tag_id 
											  and b.qstn_id=".$qid;
						foreach($conn->query($sql_fetch_qstn_tags) as $row_tags)
							echo '<span class="tag-name-section">'.$row_tags['tag_name'].'</span>';
					}
					catch(PDOException	$e)	{
						echo "Error fetching question tags!</br>";
					}
				?>
				</div>
				</br>
				<?php
					$upvotes_id='up-vote-qstn-'.$qid;
					$downvotes_id='down-vote-qstn-'.$qid;
				?>
				<!--
				<button type="button" class="btn btn-primary" onclick="window.location.href='<?php #echo $slashes; ?>qstn_ans.php?qid=<?php #echo $qid; ?>'" style="padding: 1px 2px; font-size:13px;">Answer</button>
				-->
				
				<?php
				if($logged_in == 1)	{
					$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=0 and post_id=".$qid;
					$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
										and post_type='Q' and vote_type=1 and post_id=".$qid;
					$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
					$stmt_check_up_vote->execute();
					$sql_row_0 = $stmt_check_up_vote->fetch();
					$count_row_0 = $sql_row_0['vote_count'];
					$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
					$stmt_check_down_vote->execute();
					$sql_row_1 = $stmt_check_down_vote->fetch();
					$count_row_1 = $sql_row_1['vote_count'];
				}
				else	{
					$count_row_0 = 0;
					$count_row_1 = 0;
				}
					?>
				<span class="badge view-section">
				<?php
					try	{
						$sql_fetch_views = "select views from questions where qstn_id = ".$qid;
						$stmt_fetch_views=$conn->prepare($sql_fetch_views);
						$stmt_fetch_views->execute();
						$res_views=$stmt_fetch_views->fetch();
						$view_count = $res_views['views'];
						
						echo "<div class='view-num' id='view-qstn-".$qid."'>".$view_count." views</div>";
					}
					catch(PDOException $e)	{
						
					}
				?>				
				</span>
				<span class="cont-sep">.</span>
				<input type="hidden" id="upvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_0; ?>" />
				<input type="hidden" id="downvote-value-<?php echo $qid; ?>" value="<?php echo $count_row_1; ?>" />
				<span class="vote-sec" id="up-link">
				<?php
					if($logged_in == 1)	{
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="up-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',0,'".$slashes."'";?>,document.getElementById('upvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-up-<?php echo $qid; ?>" 
						class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-upvoted":""; ?>"></span>
					<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span></span>
					<?php }
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-up"></span>
						<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
						<?php } 
					}
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-up"></span>
						<span id="up-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $up_votes; ?></span>
						<?php } ?>
				</span>
				
				<span class="vote-sec" id="down-link">
				<?php 
				if($logged_in == 1)	{
					if($posted_by != $_SESSION['user'])	{
				?>
					<span class="vote-link-area" id="down-link-area" style="cursor:pointer;"
						onclick="increaseCount(<?php echo $qid.",'".$posted_by."',1,'".$slashes."'";?>,document.getElementById('downvote-value-<?php echo $qid; ?>').value)">
						<span id="glyph-down-<?php echo $qid; ?>" class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-downvoted":""; ?>"></span>
					<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span></span>
				
				<?php }
					else	{
					?>
						<span class="glyphicon glyphicon-thumbs-down"></span>
						<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
						<?php } 
				}
						else	{
					?>
						<span class="glyphicon glyphicon-thumbs-down"></span>
						<span id="down-vote-qstn-<?php echo $qid; ?>" class="vote-count-area"><?php echo $down_votes; ?></span>
						<?php } ?>
				</span>
				<span class="cont-sep">.</span>
				<?php 
				try{
					$sql_check_bk="select count(1) as bk_cnt from bookmarks where user_id = '".$_SESSION['user']."' and post_id=".$qid;
					$stmt_chk_bk=$conn->prepare($sql_check_bk);
					$stmt_chk_bk->execute();
					$row_bk_cnt=$stmt_chk_bk->fetch();
					$bk_cnt=$row_bk_cnt['bk_cnt'];
					
					if($bk_cnt > 0)	{
						$flag=1;
						$img_src='img/svg/heart_new.svg';
					}
					else	{
						$flag=0;
						$img_src='img/svg/up.svg';
					}
				}
				catch(PDOException $e)	{
					echo $e->getMessage();
				}
				?>
				<span class="bkmrk-qstn" id="bkmrk-<?php echo $qid; ?>" data-flag="<?php echo $flag; ?>" 
					onclick="addBookmark(<?php echo "'".$slashes."',".$qid; ?>)" ><img src='<?php echo $slashes.$img_src;?>' 
					width="20" height="20" /></span>
				<span class="cont-sep">.</span>	
				<span class="ans-toggle" id="top-ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" 
					onclick="toggleAns(<?php echo $qid; ?>,1,'<?php echo $posted_by ?>')">Show top answers</a></span>
				<span class="cont-sep">.</span>	
				<span class="ans-toggle" id="ans-toggle-<?php echo $qid; ?>"><a href="javascript:void(0)" 
					onclick="toggleAns(<?php echo $qid; ?>,0,'<?php echo $posted_by ?>')">Show recent answers</a></span>
				
				</br></br>
				<div id="front-top-qstn-<?php echo $qid; ?>">
				<?php 
					try	{
						$sql_fetch_top_ans="select ans_id,
												   ans_desc,
												   up_votes,
												   down_votes,
												   posted_by,
												   created_ts 
										    from answers 
											where qstn_id = ".$qid." 
											order by up_votes desc,down_votes asc limit 2";
						/* $sql_fetch_top_ans="select a.ans_id,
													a.ans_desc,
													a.up_votes,
													a.down_votes,
													a.posted_by,
													a.ans_ts,
													a.cmnt_ts
											from													
											(
													select 
													a.ans_id,
													a.ans_desc,
													a.up_votes,
													a.down_votes,
													a.posted_by,
													a.created_ts as ans_ts,
													c.created_ts as cmnt_ts,
													case when a.ans_id=@ansid then @row:=@row+1
													else @row:=1
													end as rnum,
													@ansid:=a.ans_id
													from (select @row:=0,@ansid:=0) t,
													answers a
													inner join questions b 
													on a.qstn_id=b.qstn_id
													left outer join comments c
													on a.ans_id=c.ans_id
													where b.qstn_id=".$qid." 
											) a
											where a.rnum = 1
											order by a.ans_ts desc,a.cmnt_ts desc
  										    limit 2"; */
						$stmt_fetch_top=$conn->prepare($sql_fetch_top_ans);
						$stmt_fetch_top->execute();
						if($stmt_fetch_top->rowCount() > 0)	{
							
							while($row_top_2 = $stmt_fetch_top->fetch())	{
								
								$ansid=$row_top_2['ans_id'];
								$ans = $row_top_2['ans_desc'];
								$ans_user=$row_top_2['posted_by'];
								$ans_ts=$row_top_2['created_ts'];
								$upvotes=$row_top_2["up_votes"];
								$downvotes=$row_top_2["down_votes"];
								$sql_get_user_pic = "select pro_img_url from users where user_id='".$ans_user."'";
								
								$stmt_get_user_pic = $conn->prepare($sql_get_user_pic);
								$stmt_get_user_pic->execute();
								$row_pic = $stmt_get_user_pic->fetch();
								$ans_user_pic = $row_pic['pro_img_url'];
								?>
								<div class="ans-front-hidden-sec" id="ans-front-sec-<?php echo $ansid; ?>">
									<div class="photo-ans-sec" style="background-image:url('<?php echo $ans_user_pic; ?>'); background-size:cover;"></div>
										
									<div class="auth-text-section">
									<?php echo "<span id='ans-posted-".$ansid."' onmouseleave='showUserCard(event,1,".$ansid.",\"fa\")' onmouseenter='showUserCard(event,0,".$ansid.",\"fa\")'><a href='".$slashes."profile.php?user=".$ans_user."'>".$ans_user."</a></span> . ".get_user_date(convert_utc_to_local($ans_ts)); 
									?>
									</br>
									</div></br>
									<?php 
										$user_id_fetch=$ans_user;
										
										include "fetch_user_dtls.php";
										
										$post_type="fa";
										$user_card=$ans_user;
										$up_vote=$up_user_votes;
										$down_vote=$down_user_votes;
										$id=$ansid;
										include "user_card.php"; 
									?>
									<div class="ans-text-section"><?php echo $ans."</br>"; ?></div></br>
									<?php 
									if($logged_in == 1)	{
										$sql_check_up_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' and post_type='A' and vote_type=0 and post_id=".$ansid;
										$sql_check_down_vote = "select count(1) as vote_count from user_posts_votes where user_id='".$_SESSION['user']."' 
															and post_type='A' and vote_type=1 and post_id=".$ansid;
										$stmt_check_up_vote = $conn->prepare($sql_check_up_vote);
										$stmt_check_up_vote->execute();
										$sql_row_0 = $stmt_check_up_vote->fetch();
										$count_row_0 = $sql_row_0['vote_count'];
										$stmt_check_down_vote = $conn->prepare($sql_check_down_vote);
										$stmt_check_down_vote->execute();
										$sql_row_1 = $stmt_check_down_vote->fetch();
										$count_row_1 = $sql_row_1['vote_count'];
									}
									else	{
										$count_row_0 = 0;
										$count_row_1 = 0;
									}
										?>
										<input type="hidden" id="upvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_0; ?>" />
										<input type="hidden" id="downvote-front-value-ans-<?php echo $ansid; ?>" value="<?php echo $count_row_1; ?>" />
										
									<div class="voting-links">
										<span class="vote-sec">
									<?php 
										if($logged_in == 1)	{
												if($ans_user != $_SESSION['user'])	{
											?>
										<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
											onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,0,'<?php echo $slashes; ?>', document.getElementById('upvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
											<span id="glyph-front-up-ans-<?php echo $ansid; ?>" class="glyphicon glyphicon-thumbs-up <?php echo ($count_row_0 > 0)?"glyph-ans-upvoted":"";  ?>"></span>
										<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span></span>
										<?php } 
										}
													else	{
												?>
											<span class="glyphicon glyphicon-thumbs-up"></span>
											<span id="up-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $upvotes; ?></span>
											<?php } ?>
									</span>
									<span class="vote-sec">
									<?php 
										if($logged_in == 1)	{
												if($ans_user != $_SESSION['user'])	{
											?>
										<span href="javscript:void(0)" class="vote-link-area" style="cursor:pointer;"
											onclick="increaseAnsCount('<?php echo $ansid."','".$ans_user."'";?>,1,'<?php echo $slashes; ?>',document.getElementById('downvote-front-value-ans-<?php echo $ansid; ?>').value,2)">
											<span id="glyph-front-down-ans-<?php echo $ansid; ?>"  class="glyphicon glyphicon-thumbs-down <?php echo ($count_row_1 > 0)?"glyph-ans-downvoted":"";  ?>"></span>
										<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span></span>
									<?php } 
										}
												else	{
											?>
										<span class="glyphicon glyphicon-thumbs-down"></span>
										<span id="down-vote-front-ans-<?php echo $ansid; ?>" class="vote-count-area"><?php echo $downvotes; ?></span>
										<?php } ?>
									</span>
									&nbsp;&nbsp;
									<a id="comment-link-<?php echo $ansid; ?>" class="comment-link" href="javascript:void(0)" onclick="showComment(2,<?php echo $ansid; ?>)">Show comments</a>
									</div>
									<div class="comment-section" id="comment-front-<?php echo $ansid; ?>">
									</br>
									<div class="comments-list" id="comment-area-front-<?php echo $ansid; ?>" style="">
									<?php
										try	{
											$sql_fetch_comment="select comment_id,comment_desc,posted_by,created_ts from comments where ans_id=".$ansid;
											echo "<div class='cmnt-section' id='cmnt-list-".$ansid."'>";
											foreach($conn->query($sql_fetch_comment) as $row_cmnt)	{
												$comment_id=$row_cmnt['comment_id'];
												$comment=$row_cmnt['comment_desc'];
												$cmnt_posted_by=$row_cmnt['posted_by'];
												$created_ts = $row_cmnt['created_ts'];
												
												echo "<div class='user-comment-sec' id='comment-list-front-".$comment_id."'>".$comment." - <strong><span id='cmn-posted-".$comment_id."' onmouseleave='showUserCard(event,1,".$comment_id.",\"fc\")' onmouseenter='showUserCard(event,0,".$comment_id.",\"fc\")'><a href='".$slashes."profile.php?user=".$cmnt_posted_by."'>".$cmnt_posted_by."</a></span></strong>&nbsp;&nbsp;<span class='time-sec'>".get_user_date(convert_utc_to_local($created_ts))."</span></div>";
										
												$user_id_fetch=$cmnt_posted_by;
															
												include "fetch_user_dtls.php";
												
												$post_type="fc";
												$id=$comment_id;
												$user_card=$cmnt_posted_by;
												$up_vote=$up_user_votes;
												$down_vote=$down_user_votes;
												include "user_card.php"; 
											}
											echo "</div>";
										}
										catch(PDOException $e)	{
											echo "Internal server error";
										}
									?>
									<input type="text" class="form-control comment-inp" id="comment-front-ans-<?php echo $ansid; ?>" placeholder="Leave comment" onfocus="showAlert(0,<?php echo $logged_in; ?>)"
									onkeypress="addComment(event,2,<?php echo "'".$slashes."',".$ansid.",'".$ans_user."',".$qid.",'".$posted_by."'"; ?>)"/>
									</br></br>
									</div>
								</div></br>									
								</div>
								<?php
							}
						}
						else	{
							echo "<div class='no-ans-section'>No answers to this question yet. Be the first one to answer.</div>";
						}
						
					}
					catch(PDOException $e)	{
						echo "Some error occurred";
					}
				?>
					
				</div>
				<div class="recent-ans">
					<div class="toggle-ans-sec" id="toggle-ans-sec-<?php echo $qid; ?>" 
						data-load="0" >
					<!--	onscroll="fetchAnswers(<?php #echo $qid; ?>,'<?php #echo $slashes; ?>','r','<?php #echo $posted_by; ?>')"  --> 
						<?php include $slashes."recent_ans.php"; 
						if($row_num > 4)	{
						?>
						<div class="ans-load" id="ans-load-<?php echo $qid; ?>">
							<div id="btn-ans-section">
								<button id="explore-ans-btn" class="btn btn-primary" onclick="fetchAnswers(<?php echo $qid; ?>,'<?php echo $slashes; ?>','r','<?php echo $posted_by; ?>')">More recent answers</button>
							</div>
						</div>
						<?php 	}	?>
					</div>
				</div>
				<div class="top-ans" >
					<div class="toggle-top-ans-sec" id="toggle-top-ans-sec-<?php echo $qid; ?>" 
						data-load="0" >
					<!--	onscroll="fetchAnswers(<?php #echo $qid; ?>,'<?php #echo $slashes; ?>','t','<?php #echo $posted_by; ?>')" --> 
						<?php include $slashes."top_ans.php"; 
						if($row_num > 4)	{
						?>
						<div class="ans-load" id="ans-top-load-<?php echo $qid; ?>">
							<div id="btn-ans-section">
								<button id="explore-top-ans-btn" class="btn btn-primary" onclick="fetchAnswers(<?php echo $qid; ?>,'<?php echo $slashes; ?>','t','<?php echo $posted_by; ?>')">More top answers</button>
							</div>
						</div>
						<?php 	}	?>
					</div>
				</div>
				</br>
				<div style="font-size:14px;color:#65A668;" class "ans-msg" id="ans-msg-<?php echo $qid; ?>" ></div>
				
				<div class="user-ans-section">
					<input type="text" class="form-control ans-inp" id="ans-<?php echo $qid; ?>" onfocus="showAlert(1,<?php echo $logged_in; ?>)" placeholder="Your answer here" 
					onkeypress="postAnswer(event,'<?php echo $slashes; ?>',this.value,<?php echo $qid.",'".$posted_by."'"; ?>,0)"/>
					</br>
				</div>
				</div></br>
				<?php 
					}
	}
	$start_qstn+=1;
}


	?>
