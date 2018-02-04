<div class="container">
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
						<div id="media-image"><img src="img/logo.jpg" width="200" height="50"/></div>
					</td>
				</tr>
			</table></br>
			<div id="page-title"><span>User dashboard</span></div></br>
			<div class="row">
				<div class="col-sm-3">
					<div id="row-1">
						<a href="qstn.php" class="btn btn-info"><span class="glyphicon glyphicon-question-sign"></span>&nbsp;&nbsp;Ask </a>&nbsp;
						<div id="show-notify-section-mobile"></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div id="row-2">
						<input id="srch-qstn-mob" type="text" class="form-control" id="srch-box-media" placeholder="Search questions" onkeypress="fetchQuestionsMobile(this.value,'<?php echo $slashes; ?>')" />
					
						<div id="srch-result-mobile"></div>
					</div>
				</div>
			</div>
		</div>
		<div id="options-menu">
			<div class="row">
				<div class="col-sm-12">
					<a class="btn btn-primary" href="index.php">Log in / Register</a></br></br>
				</div>
			</div></br>

			</br>
			<ul class="nav nav-pills nav-stacked">
				<li><a href="dashboard.php"><span class="glyphicon glyphicon-home" ></span>&nbsp;Dashboard</a></li>
				<li><a href="forum" ><span class="glyphicon glyphicon-question-sign"></span>&nbsp;Q/A Forum</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-transfer" ></span>&nbsp;Expert Connect</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-refresh" ></span>&nbsp;Collaborate</a></li>
				<li><a href="" ><span class="glyphicon glyphicon-gift" ></span>&nbsp;Favours</a></li>
			</ul>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<div id="head-bottom"></div>
				
			</div>
		</div></br>
		<div class="row">
			<div class="col-sm-2" id="main-side-column" style="background:#FBFBFB;"></br>
				<a class="btn btn-primary" href="index.php">Log in / Register</a></br></br>
				<p id="col1-text">Sciencemarket is an online discussion platform where people ask questions, answer questions, connect with experts, collaborate with people and provide favours to the expert. If you think you need any assistance in your academics, you are in the right place. </br>
				</br>
				Click on Login / Register to get unlimited access to stock of scientific questions.
				</p>
				</br>
				
			</div>
			<div class="col-sm-7" id="middle-container">
				<div class="db-guest-menu">
					<a class="db-link" id="recent-link-db" href="dashboard.php" onclick="$(this).addClass('active-link')">Recent Posts</a>
					<a class="db-link" id="mup-link-db" href="dashboard.php?dboardToken=upvoted" onclick="$(this).addClass('active-link')">Most upvoted Posts</a>
				</div></br>
				<?php
				try	{
					if(isset($_GET['dboardToken']) and $_GET['dboardToken']=='upvoted')	{
						$sql="select * from questions order by up_votes desc";
						?>
						<script>
							$("#mup-link-db").addClass("active-link");
						</script>
						<?php
					}
					else	{
						$sql="select * from questions order by created_ts desc";
						?>
						<script>
							$("#recent-link-db").addClass("active-link");
						</script>
						<?php
					}
					include "forum/fetch_answers1.php";
					if($stmt->rowCount() <=0)	{
						echo '<div class="alert alert-info">
							  We can\'t find any relevant questions for you. Please add some interests so that we can display questions which might be relevant to you. <strong><a href="profile.php">Click here</a></strong>
						  </div>';
					}
					else
						echo '<div class="alert alert-info">
							  No recent questions
						  </div>';
				}
				catch(PDOException	$e)	{
					echo '';
				}
				?>
			</div>
			<div class="col-sm-3">
				<div id="tags-box">
					<span id="tag-box-title">Popular tags</span></br></br>
					<?php
						try	{
							$sql_fetch_tags_list="  select a.tag_id,a.tag_name,count(1) as cnt_follower
													from tags a
													inner join qstn_tags b 
													on a.tag_id = b.tag_id
													group by a.tag_id,a.tag_name
													order by 3 desc limit 50
												  ";
							$stmt_fetch_tags_list=$conn->prepare($sql_fetch_tags_list);
							$stmt_fetch_tags_list->execute();
							
							if($stmt_fetch_tags_list->rowCount() < 0)	{
								echo "Nothing to show now";
							}
							else	{
								while($row_tags_list=$stmt_fetch_tags_list->fetch())	{
									$tag_id=$row_tags_list['tag_id'];
									$tag_nm=$row_tags_list['tag_name'];
									$count_tags=$row_tags_list['cnt_follower'];
									
									echo "<span class='badge tag-name-list'><a href='forum/index.php?tag=".$tag_id."'>".$tag_nm."</a></span>";
								}
							}
						}
						catch(PDOException $e)	{
							echo "Some error occured in the server";
						}
					
					
					?>
					</br></br>
				</div></br>
			</div>
		</div>
	</div>
