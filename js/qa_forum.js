$(document).ready(function()	{
	
	$("#nav-id").click(function(e)	{
		$("#options-menu").show('slide', {direction: 'left'}, 500);
		$("#block").show();
		e.stopPropagation();
	});
	$("#block").click(function()	{
		$("#options-menu").hide('slide', {direction: 'left'}, 500);
		$("#block").hide();
	});
	var z=$("#qstn-res .qstn_row").length;
	if(z == 10)	{
		document.getElementById("scroll-flag").value=1;
	}
	else if(z < 10) {
		$("#scroll-msg").hide();
		document.getElementById("scroll-flag").value=0;
	}
}); 
function getHeight()	{
	var x=$(document).scrollTop();
	if(x >= 52)	{
		$("#header-container").css("position","fixed");
		$("#header-container").css("width","100%");
		$("#go-top-btn").show();
	}
	else{
		$("#go-top-btn").hide();
		$("#header-container").css("position","relative");
		$("#header-container").css("width","100%");
	}
	
	var scrollPosition = $(window).height() + $(window).scrollTop();
	var scrollHeight = $(document).height();
	
	var scrollFlag = document.getElementById("scroll-flag").value;
 	if ((scrollHeight - scrollPosition)/scrollHeight == 0 && scrollFlag==1) {
		var x=document.getElementById("qid-array-list").value;
		var y=document.getElementById("page-locate-data").value;
		var z=$("#qstn-res .qstn_row").length;
		fetchMoreQuestions(x,y,z);
    } 
	
}

function fetchAnswers(qid,slashes,source,qstn_user)	{
	$("#ans-load-"+qid).show();
	var sectionId = "";
	if(source == 'r')	{
		sectionId = "toggle-ans-sec-"+qid;
		sectionClass = "ans-hidden-sec";
		ansList = "ans-list-qid-"+qid;
		scrollFlag = "scroll-flag-"+qid;
	}
	else if(source == 't')	{
		sectionId = "toggle-top-ans-sec-"+qid;
		sectionClass = "ans-hidden-top-sec";
		ansList = "ans-top-list-qid-"+qid;
		scrollFlag = "scroll-top-flag-"+qid;
	}
	var idLen = $("#"+sectionId+" ."+sectionClass).length;
	
	var scrollTop=document.getElementById(sectionId).scrollTop;  
    var scrollheight=document.getElementById(sectionId).scrollHeight;  
    var windowheight=document.getElementById(sectionId).clientHeight;  
    var scrolloffset=20;  
	var scrollCheck = document.getElementById(scrollFlag).value;
	if(scrollTop == (scrollheight-windowheight) && scrollCheck=='1')	{
		
		$.ajax({
			type:"post",
			url:slashes+"fetch_ans.php",
			data:
			{	
				"qid":qid,
				"qstn_user":qstn_user,
				"ans_list":document.getElementById(ansList).value,
				"root":slashes,
				"answers":idLen,
				"source_flag":source
			},
			success:function(res)	{
				if(res == "0")	{
					$("#ans-load-"+qid).text("No more answers");
					$("#"+scrollFlag).val("0");
				}
					
				else
					$("#"+sectionId).append(res);
			}
		});
	}
}
function fetchMoreQuestions(x,y,z)	{
	
	$.ajax({
		type:"post",
		url:y+"fetch_qstns.php",
		data:
		{
			"qstn_list":x,
			"root":y,
			"questions":z
			},
		success:function(res)	{
			if(res == "0")	{
				$("#scroll-msg").text("Looking for more questions? Explore more in forum page");
				$("#scroll-flag").val("0");
			}
				
			else
				$("#qstn-res").append(res);
		}
	});
}

function postAnswer(e,slashes,val,qid,postedBy,flag)	{
	var keyCode = e.keyCode || e.which;
	if(keyCode == 13 && val.trim()!="")	{
		$.ajax({
			type:"post",
			url:slashes+"post_ans.php",
			data:{
				"ans":val,
				"qid":qid,
				"postedBy":postedBy,
				"slashes":slashes,
				"flag":flag
			},
			beforeSend:function()	{
				$("#ans-"+qid).attr("disabled","disabled");
			},
			success:function(res)	{
				//$("#ans-"+qid).removeAttr("disabled");
				$("#ans-"+qid).hide();
				var txt="";
				document.getElementById("ans-"+qid).value="";
				
				if($("#toggle-ans-sec-"+qid).is(':visible'))
					$("#toggle-ans-sec-"+qid).hide();
				else if($("#toggle-top-ans-sec-"+qid).is(':visible'))
					$("#toggle-top-ans-sec-"+qid).hide();
				
				$("#front-top-qstn-"+qid).show();
				if(res == 0)
					txt="Some error occurred. We are trying to fix the issues";
				else if(res == -1)
					txt="Internal server error";
				else if(res == -2)
					txt="Please type an answer";
				else	{
					document.getElementById("front-top-qstn-"+qid).innerHTML=res;
					txt="Thank you! Your answer has been posted";
				}
				$("#ans-msg-"+qid).text(txt);
			}
		});
	}
}
function toggleAns(qid,x)	{
	$("#front-top-qstn-"+qid).hide();
	if(x == 0)	{
		var query1 = $("#toggle-ans-sec-"+qid).is(':visible');
		var query2 = $("#toggle-top-ans-sec-"+qid).is(':visible');
		if(query1 == true)	{
		//	$("#toggle-ans-sec-"+qid).slideUp();
		//	$("#front-top-qstn-"+qid).show();
		}
		else if(query2 == true)	{
			$("#toggle-top-ans-sec-"+qid).hide();
			$("#toggle-ans-sec-"+qid).show();
		}
		else	{
			$("#toggle-ans-sec-"+qid).slideDown();
		}
	}
	else if(x == 1)	{
		var query1 = $("#toggle-top-ans-sec-"+qid).is(':visible');
		var query2 = $("#toggle-ans-sec-"+qid).is(':visible');
		if(query1 == true)	{
		//	$("#toggle-top-ans-sec-"+qid).slideUp();
		//	$("#front-top-qstn-"+qid).show();
		}
		else if(query2 == true)	{
			$("#toggle-ans-sec-"+qid).hide();
			$("#toggle-top-ans-sec-"+qid).show();
		}
		else	{
			$("#toggle-top-ans-sec-"+qid).slideDown();
		}
	}
	//$("div#"+eventId).slideToggle();
}

function showQstn(flag)	{
	//alert(flag);
	
	$.ajax(
	{
		type:"post",
		url:"load_qstn_home.php",
		data:{
			"flag":flag,
			"token":0
				},
		success:function(result)	{
			$('#qstn-res').html(result);
		}
	}
	);
}

function sortQuestions(topic,txt)	{
	var token;
	if(txt=="Most up-voted")
		token=1;
	else if(txt=="Recent")
		token=2;
	else if(txt=="Most viewed")
		token=3;
	else
		token=4;
	$.ajax({
		type:"post",
		url:"load_qstn_home.php",
		data:{
			"flag":topic,
			"token":token
		},
		success:function(result)	{
			$('#qstn-res').html(result);
		}
	});
}
function showSubTopics(id)	{
	var id_res="#"+id;
	$(id_res).slideToggle();
}	
function showTopics(id)	{
	var id_res="."+id;
	$(id_res).slideToggle();
}	

function increaseCount(id,userid,func,rootLocation,voteCheckCount)	{
	var sectionId,requestType;
	if(func=="0")	{
		sectionId="#up-vote-qstn-"+id;
		var upVoteVal = parseInt($(sectionId).text());
	}
	else if(func=="1")	{
		sectionId="#down-vote-qstn-"+id;
		var downVoteVal = parseInt($(sectionId).text());
	}
	if(func=="0")	{
		if(voteCheckCount > 0)	{
			$("#glyph-up-"+id).removeClass("glyph-upvoted");
			requestType="D";
			$("#upvote-value-"+id).val("0");
			$(sectionId).text(upVoteVal-1);
		}
		else	{
			$("#glyph-up-"+id).addClass("glyph-upvoted");
			requestType="A";
			$("#upvote-value-"+id).val("1");
			$(sectionId).text(upVoteVal+1);
		}
	}
	else if(func=="1")	{
		if(voteCheckCount > 0)	{
			$("#glyph-down-"+id).removeClass("glyph-downvoted");
			requestType="D";
			$("#downvote-value-"+id).val("0");
			$(sectionId).text(downVoteVal-1);
		}
		else	{
			$("#glyph-down-"+id).addClass("glyph-downvoted");
			requestType="A";
			$("#downvote-value-"+id).val("1");
			$(sectionId).text(downVoteVal+1);
			
		}
	}
	
	$.ajax({
		type:"post",
		url:rootLocation+"update_votes1.php",
		data:{
			"id":id,
			"userid":userid,
			"func":func,
			"requestType":requestType,
			"qaflag":"0"
		}
		/* success:function(result)	{
			$(sectionId).html(result);
		} */
	});
}
function increaseAnsCount(id,userid,func,rootLocation,voteCheckCount,flag)	{
	var upVoteVal,downVoteVal,sectionId,bothFlag=false;
	var upVoteDivFront=$("#up-vote-front-ans-"+id).length;
	var downVoteDivFront=$("#down-vote-front-ans-"+id).length;
	
	if(func=="0")	{
		var upVoteDivRecent=$("#up-vote-ans-"+id).length;
		var upVoteDivTop=$("#up-vote-top-ans-"+id).length;
		
		if(upVoteDivRecent && upVoteDivTop)	{
			sectionId="#up-vote-ans-"+id;
			bothFlag=true;
		}
		else if(upVoteDivRecent)
			sectionId="#up-vote-ans-"+id;
		else if(upVoteDivTop)
			sectionId="#up-vote-top-ans-"+id;
		
		if(upVoteDivFront)	{
			upVoteVal = parseInt($("#up-vote-front-ans-"+id).text());
		}
		else
			upVoteVal = parseInt($(sectionId).text());
		
	}
	else if(func=="1")	{
		var downVoteDivRecent=$("#down-vote-ans-"+id).length;
		var downVoteDivTop=$("#down-vote-top-ans-"+id).length;
		if(downVoteDivRecent && downVoteDivTop)	{
			sectionId="#down-vote-ans-"+id;
			bothFlag=true;
		}
		else if(downVoteDivRecent)
			sectionId="#down-vote-ans-"+id;
		else if(downVoteDivTop)
			sectionId="#down-vote-top-ans-"+id;
		
		downVoteVal = parseInt($(sectionId).text());
		if(downVoteDivFront)	{
			downVoteVal = parseInt($("#down-vote-front-ans-"+id).text());
		}
	}
	
	if(func=="0")	{
		if(voteCheckCount > 0)	{
			
			if(flag==0)	{
				$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
				$("#upvote-value-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-up-top-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("0");
				}
				
				if(upVoteDivFront)	{
					$("#glyph-front-up-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-front-value-ans-"+id).val("0");
				}
			}
			else if(flag==1)	{
				$("#glyph-up-top-ans-"+id).removeClass("glyph-ans-upvoted");
				$("#upvote-value-top-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("0");
				}
				
				if(upVoteDivFront)	{
					$("#glyph-front-up-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-front-value-ans-"+id).val("0");
				}
			}
			else if(flag==2)	{
				$("#glyph-front-up-ans-"+id).removeClass("glyph-ans-upvoted");
				$("#upvote-front-value-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("0");
					$("#glyph-up-top-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("0");
				}
				else if(upVoteDivRecent)	{
					$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("0");
				}
				else if(upVoteDivTop)	{
					$("#glyph-up-top-ans-"+id).removeClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("0");
				}
			}
			requestType="D";
			if(bothFlag)	{
				$("#up-vote-ans-"+id).text(upVoteVal-1);
				$("#up-vote-top-ans-"+id).text(upVoteVal-1);
			}
			else
				$(sectionId).text(upVoteVal-1);
			if(upVoteDivFront)	{
				$("#up-vote-front-ans-"+id).text(upVoteVal-1);
			}
		}
		else	{
			if(flag==0)	{
				$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
				$("#upvote-value-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-up-top-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("1");	
				}
				if(upVoteDivFront)	{
					$("#glyph-front-up-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-front-value-ans-"+id).val("1");
				}
			}
			else if(flag==1)	{
				$("#glyph-up-top-ans-"+id).addClass("glyph-ans-upvoted");
				$("#upvote-value-top-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("1");
				}
				if(upVoteDivFront)	{
					$("#glyph-front-up-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-front-value-ans-"+id).val("1");
				}
			}
			else if(flag==2)	{
				$("#glyph-front-up-ans-"+id).addClass("glyph-ans-upvoted");
				$("#upvote-front-value-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("1");
					$("#glyph-up-top-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("1");
				}
				else if(upVoteDivRecent)	{
					$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-ans-"+id).val("1");
				}
				else if(upVoteDivTop)	{
					$("#glyph-up-top-ans-"+id).addClass("glyph-ans-upvoted");
					$("#upvote-value-top-ans-"+id).val("1");
				}
			}
			requestType="A";
			if(bothFlag)	{
				$("#up-vote-ans-"+id).text(upVoteVal+1);
				$("#up-vote-top-ans-"+id).text(upVoteVal+1);
			}
			else
				$(sectionId).text(upVoteVal+1);
			
			if(upVoteDivFront)	{
				$("#up-vote-front-ans-"+id).text(upVoteVal+1);
			}
		}
	}
	else if(func=="1")	{
		if(voteCheckCount > 0)	{
			if(flag==0)	{
				$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
				$("#downvote-value-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-down-top-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("0");	
				}
				if(downVoteDivFront)	{
					$("#glyph-front-down-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-front-value-ans-"+id).val("0");
				}
			}
			else if(flag==1)	{
				$("#glyph-down-top-ans-"+id).removeClass("glyph-ans-downvoted");
				$("#downvote-value-top-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("0");
				}
				if(downVoteDivFront)	{
					$("#glyph-front-down-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-front-value-ans-"+id).val("0");
				}
			}
			else if(flag==2)	{
				$("#glyph-front-down-ans-"+id).removeClass("glyph-ans-downvoted");
				$("#downvote-front-value-ans-"+id).val("0");
				if(bothFlag)	{
					$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("0");
					$("#glyph-down-top-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("0");
				}
				else if(upVoteDivRecent)	{
					$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("0");
				}
				else if(upVoteDivTop)	{
					$("#glyph-down-top-ans-"+id).removeClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("0");
				}
			}
			requestType="D";
			if(bothFlag)	{
				$("#down-vote-ans-"+id).text(downVoteVal-1);
				$("#down-vote-top-ans-"+id).text(downVoteVal-1);
			}
			else
				$(sectionId).text(downVoteVal-1);
			if(downVoteDivFront)	{
				$("#down-vote-front-ans-"+id).text(downVoteVal-1);
			}
		}
		else	{
			if(flag==0)	{
				$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
				$("#downvote-value-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-down-top-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("1");
				}
				if(downVoteDivFront)	{
					$("#glyph-front-down-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-front-value-ans-"+id).val("1");
				}
			}
			else if(flag==1)	{
				$("#glyph-down-top-ans-"+id).addClass("glyph-ans-downvoted");
				$("#downvote-value-top-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("1");
				}
				if(downVoteDivFront)	{
					$("#glyph-front-down-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-front-value-ans-"+id).val("1");
				}
				
			}
			else if(flag==2)	{
				$("#glyph-front-down-ans-"+id).addClass("glyph-ans-downvoted");
				$("#downvote-front-value-ans-"+id).val("1");
				if(bothFlag)	{
					$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("1");
					$("#glyph-down-top-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("1");
				}
				else if(upVoteDivRecent)	{
					$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-ans-"+id).val("1");
				}
				else if(upVoteDivTop)	{
					$("#glyph-down-top-ans-"+id).addClass("glyph-ans-downvoted");
					$("#downvote-value-top-ans-"+id).val("1");
				}
			}
			requestType="A";
			if(bothFlag)	{
				$("#down-vote-ans-"+id).text(downVoteVal+1);
				$("#down-vote-top-ans-"+id).text(downVoteVal+1);
			}
			else
				$(sectionId).text(downVoteVal+1);
			if(downVoteDivFront)	{
				$("#down-vote-front-ans-"+id).text(downVoteVal+1);
			}
		}
	}
	$.ajax({
		type:"post",
		url:rootLocation+"update_votes1.php",
		data:{
			"id":id,
			"userid":userid,
			"func":func,
			"requestType":requestType,
			"qaflag":"1"
		}
		/* success:function(result)	{
			$(sectionId).html(result);
		} */
	});
}

function addComment(token,root,ansid,ans_posted_by,qid,qstn_posted_by)	{
	var commentInpId = "",idRes="";
	if(token == 0)	{
		commentInpId="comment-ans-"+ansid;
		idRes = "comment-area-"+ansid;
		loadTextId = "comment-load-recent-text-"+ansid;
	}
	else if(token == 1)	{
		commentInpId="comment-top-ans-"+ansid;
		idRes = "comment-area-top-"+ansid;
		loadTextId = "comment-load-top-text-"+ansid;
	}
	else if(token == 2)	{
		commentInpId="comment-front-ans-"+ansid;
		idRes = "comment-area-front-"+ansid;
		loadTextId = "comment-load-front-text-"+ansid;
	}
	var commentVal = document.getElementById(commentInpId).value;
	
	if((commentVal.trim().length) != 0)	{
		$("#"+commentInpId).attr("disabled", "disabled"); 
		$.ajax(
		{
			type:"post",
			url:root+"add_comment.php",
			data:
				{
					"ansid":ansid,
					"text":commentVal,
					"posted_by":ans_posted_by,
					"qid":qid,
					"q_posted_by":qstn_posted_by
				},
			beforeSend:function()	{
			},
			success:function(result)	{
				$("#"+idRes).html(result);
				var scrollHeight = document.getElementById(idRes).scrollHeight;
				var clientHeight = document.getElementById(idRes).clientHeight;
				document.getElementById(idRes).scrollTop=(scrollHeight-clientHeight);
				$("#"+loadTextId).hide();
				document.getElementById(commentInpId).value="";
				$("#"+commentInpId).removeAttr("disabled");
			}
		}
		)
	}
}
function showComment(token,ansid)	{
	if(token == 0)	{
		var query1 = $("#comment-recent-"+ansid).is(':visible');
		if(query1)	{
			$("#comment-recent-"+ansid).hide();
			$("#comment-recent-link-"+ansid).text("Show comments");
		}
		else	{
			$("#comment-recent-"+ansid).show();
			$("#comment-recent-link-"+ansid).text("Hide comments");
		}
	}
	else if(token == 1)	{
		var query1 = $("#comment-top-"+ansid).is(':visible');
		if(query1)	{
			$("#comment-top-"+ansid).hide();
			$("#comment-top-link-"+ansid).text("Show comments");
		}
		else	{
			$("#comment-top-"+ansid).show();
			$("#comment-top-link-"+ansid).text("Hide comments");
		}
	}
	else if(token == 2)	{
		var query1 = $("#comment-front-"+ansid).is(':visible');
		if(query1)	{
			$("#comment-front-"+ansid).hide();
			$("#comment-link-"+ansid).text("Show comments");
		}
		else	{
			$("#comment-front-"+ansid).show();
			$("#comment-link-"+ansid).text("Hide comments");
		}
	}
}

function loadMoreComments(token,root,ansid)	{
	var childNodes="",cidList="";
	if(token == 0)	{
		childNodes=$("#comment-area-"+ansid+" .user-comment-sec").length;
		cidList=document.getElementById("cid-recent-section-"+ansid).value;
	}
	else if(token == 1)	{
		childNodes=$("#comment-area-top-"+ansid+" .user-comment-sec").length;
		cidList=document.getElementById("cid-top-section-"+ansid).value;
	}
	else if(token == 2)	{
		childNodes=$("#comment-area-front-"+ansid+" .user-comment-sec").length;
		cidList=document.getElementById("cid-front-section-"+ansid).value;
	}
	$.ajax({
		type:"post",
		url:root+"fetch_comments.php",
		data:
		{
			"flag":token,
			"ansid":ansid,
			"cid_list":cidList,
			"element_count":childNodes
		},
		success:function(res)	{
			if(token == 0)	{
				if(res==0)	{
					$("#comment-load-recent-text-"+ansid).hide();
				}
				else
					$("#comment-area-"+ansid).append(res);
			}
			if(token == 1)	{
				if(res==0)	{
					$("#comment-load-top-text-"+ansid).hide();
				}
				else
					$("#comment-area-top-"+ansid).append(res);
			}
			if(token == 2)	{
				if(res==0)	{
					$("#comment-load-front-text-"+ansid).hide();
				}
				else
					$("#comment-area-front-"+ansid).append(res);
			}
		}
	});
}

function showUserCard(token,qid)	{
	if(token == 0)	{
		$("#user-card-"+qid).fadeIn(200);
	}
	else if(token == 1)	{
		$("#user-card-"+qid).delay(300).fadeOut('fast');
	}
	else if(token == 2)	{
		$("#user-card-"+qid).stop(true,false).show();
	}
} 
function updateFollower(userid,flag,id)	{
	$.ajax({
		type:"post",
		url:"updt_follower.php",
		data:
		{
			"user_id":userid,
			"flag":flag
		},
		success:function(result)	{
			if(flag == 0)	{
				$("#unfollow-"+id).removeClass("disabled btn-disabled");
				$("#follow-"+id).addClass("disabled btn-disabled");
				$("#follow-"+id).removeAttr("onclick");
				$("#unfollow-"+id).attr("onclick","updateFollower('"+userid+"',1,'"+id+"')");
			}
			else if(flag == 1)	{
				$("#follow-"+id).removeClass("disabled btn-disabled");
				$("#unfollow-"+id).addClass("disabled btn-disabled");
				$("#follow-"+id).attr("onclick","updateFollower('"+userid+"',0,'"+id+"')");
				$("#unfollow-"+id).removeAttr("onclick");
			}
			$("#follow-message-"+id).html(result);
		}
	});
}
