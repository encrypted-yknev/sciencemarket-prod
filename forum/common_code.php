<?php
	
	$count_slash = substr_count($_SERVER['PHP_SELF'],'/');
	if($count_slash == 2)	{
		$slashes_main = '../';
		$slashes_cur = '';
	}
	else if($count_slash == 3)	{
		$slashes_main = '../../';
		$slashes_cur = '../';
	}
	else if($count_slash == 4)	{
		$slashes_main = '../../../';
		$slashes_cur = '../../';
	}
	
?>

		<div id="side-nav">
			<table border="0">
				<tr>
					<td>
						<div id="nav-id">
							<div class="side-bar"></div>
							<div class="side-bar"></div>
							<div class="side-bar"></div>
						</div>
					</td>
					<td>
						<div id="media-image"><img src="<?php echo $slashes_main; ?>img/logo.jpg" width="200" height="50"/></div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>Q/A Forum</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-1">
						<a href="<?php echo $slashes_main; ?>qstn.php" class="btn btn-info">Ask Questions</a>
					</div>
					
				</div>
				<div class="col-sm-6">
					<div id="row-2">
						<input type="text" class="form-control" id="srch-box-media" placeholder="Search questions" onkeypress="fetchQuestionsMobile(this.value,'<?php echo $slashes_main; ?>')" />
						<div id="srch-result-mobile"></div> 
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<li class="side-menu"><a href="<?php echo $slashes_cur; ?>myposts" class="list-group-item" >My Posts</a></li>
			<li class="side-menu"><a href="<?php echo $slashes_cur; ?>relevant" class="list-group-item" >Relevant</a></li>
			<li class="side-menu"><a href="<?php echo $slashes_cur; ?>upvoted" class="list-group-item" >Most upvoted</a></li>
			<li class="side-menu"><a href="<?php echo $slashes_cur; ?>recent" class="list-group-item" >Recent </a></li>
			<li class="side-menu" id="side-menu-media-opt4"><a href="javascript:void(0)" class="list-group-item" onclick="showTopics('side-menu-topics')" >+ Topic based</a>
				<?php
				try	{
					$sql_fetch_topics="select topic_id,topic_desc from topics where parent_topic = 0";
					foreach($conn->query($sql_fetch_topics) as $row_topics)	{
						$topic_name = $row_topics['topic_desc'];
						$topic_id = $row_topics['topic_id'];
						if($topic_id == 7)	{
							$link_to = $slashes_cur.'topics/theory';
						}
						else if($topic_id == 8)	{
							$link_to = $slashes_cur.'topics/data-analysis';
						}
						elseif($topic_id == 9)	{
							$link_to = $slashes_cur.'topics/experiment';
						}
						else if($topic_id == 10)	{
							$link_to = $slashes_cur.'topics/cell';
						}
						
						echo 
						'<li class="side-menu-topics"><a href="'.$link_to.'" >'.$topic_name.'</a></li>';
					}	
				}
				catch(PDOException $e)	{
					echo 'Error fetching topics';
				} 
				?>
			</li></br>
			<ul class="nav nav-pills nav-stacked">
				<li><a href="<?php echo $slashes_main; ?>profile.php" ><span class="glyphicon glyphicon-user" ></span>&nbsp;My Profile</a></li>
				<li><a href="<?php echo $slashes_main; ?>dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
				<li><a href="<?php echo $slashes_main; ?>expert_connect.php" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
				<li><a href="<?php echo $slashes_main; ?>logout.php"><span class="glyphicon glyphicon-off"></span>&nbsp;Logout</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<div id="head-bottom"></div>
				
			</div>
		</div></br>
		<div class="row">
			<div class="col-sm-2" id="side-qstn-cat" style="background-color:#F1F1F1;">
				<li class="side-menu"><a href="<?php echo $slashes_cur; ?>myposts">My Posts</a></li>
				<li class="side-menu"><a href="<?php echo $slashes_cur; ?>relevant" >Relevant</a></li>
				<li class="side-menu"><a href="<?php echo $slashes_cur; ?>upvoted" >Most upvoted</a></li>
				<li class="side-menu"><a href="<?php echo $slashes_cur; ?>recent" >Recent </a></li>
				<li class="side-menu" id="side-menu-opt4"><a href="javascript:void(0)" onclick="showTopics('side-menu-topics')" >+ Topic based</a>
					<?php
					try	{
						$sql_fetch_topics="select topic_id,topic_desc from topics where parent_topic = 0";
						foreach($conn->query($sql_fetch_topics) as $row_topics)	{
							$topic_name = $row_topics['topic_desc'];
							$topic_id = $row_topics['topic_id'];
							if($topic_id == 7)	{
								$link_to = $slashes_cur.'topics/theory';
							}
							else if($topic_id == 8)	{
								$link_to = $slashes_cur.'topics/data-analysis';
							}
							elseif($topic_id == 9)	{
								$link_to = $slashes_cur.'topics/experiment';
							}
							else if($topic_id == 10)	{
								$link_to = $slashes_cur.'topics/cell';
							}
							
							echo 
							'<li class="side-menu-topics"><a href="'.$link_to.'" >'.$topic_name.'</a></li>';
						}
					}
					catch(PDOException $e)	{
						echo 'Error fetching topics';
					} 
					?>
				</li>
			</div>