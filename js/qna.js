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
	

 });

function increaseCount(id,userid,func,voteCheckCount)	{
	var sectionId;
	if(func=="0")	{
		sectionId="#up-vote-ans-"+id;
		var upVoteVal = parseInt($(sectionId).text());
	}
	else if(func=="1")	{
		sectionId="#down-vote-ans-"+id;
		var downVoteVal = parseInt($(sectionId).text());
	}
	
	if(func=="0")	{
		if(voteCheckCount > 0)	{
			$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
			requestType="D";
			$("#upvote-value-ans-"+id).val("0");
			$(sectionId).text(upVoteVal-1);
		}
		else	{
			$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
			requestType="A";
			$("#upvote-value-ans-"+id).val("1");
			$(sectionId).text(upVoteVal+1);
		}
	}
	else if(func=="1")	{
		if(voteCheckCount > 0)	{
			$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
			requestType="D";
			$("#downvote-value-ans-"+id).val("0");
			$(sectionId).text(downVoteVal-1);
		}
		else	{
			$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
			requestType="A";
			$("#downvote-value-ans-"+id).val("1");
			$(sectionId).text(downVoteVal+1);
			
		}
	}
	$.ajax({
		type:"post",
		url:"update_votes1.php",
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

function loadAnswerList(qid,postedBy)	{
	var ans=CKEDITOR.instances.userans.getData();
	$.ajax({
		type:"post",
		url:"load_answers.php",
		data:{
			"ans":ans,
			"qid":qid,
			"postedBy":postedBy
		},
		beforeSend:function()	{
			$("#block-container").show();
			$("#load-section").show();
			$(".msg-section").hide();
		},
		success:function(result)	{
			CKEDITOR.instances.userans.setData("");
			$("#ans_container").html(result);
		},
		complete:function()	{
			$("#block-container").hide();
			$("#load-section").hide();
			$("#user-ans").val("");
		}
		
	});

}

function showAlert(flag,loggedIn)	{
	
	if(loggedIn==0)	{
		if(flag == 0)	{
			$(".comment-textbox").val("");
			$(".comment-textbox").blur();
			alert('Please login to post a comment');
		}
	}
}

function showUserCard(event,token,qid,type)	{
	if(token == 0)	{
		$("#user-card-"+type+"-"+qid).fadeIn(200);
	}
	else if(token == 1)	{
		$("#user-card-"+type+"-"+qid).delay(300).fadeOut('fast');
	} 
	else if(token == 2)	{
		$("#user-card-"+type+"-"+qid).stop(true,false).show();
	}
} 
function updateFollower(slash,userid,flag,id,loggedIn,type)	{
	if(loggedIn == 1)	{
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
					$("#unfollow-"+type+"-"+id).removeClass("disabled btn-disabled");
					$("#follow-"+type+"-"+id).addClass("disabled btn-disabled");
					$("#follow-"+type+"-"+id).removeAttr("onclick");
					$("#unfollow-"+type+"-"+id).attr("onclick","updateFollower('','"+userid+"',1,"+id+",1,'"+type+"')");
				}
				else if(flag == 1)	{
					$("#follow-"+type+"-"+id).removeClass("disabled btn-disabled");
					$("#unfollow-"+type+"-"+id).addClass("disabled btn-disabled");
					$("#follow-"+type+"-"+id).attr("onclick","updateFollower('','"+userid+"',0,"+id+",1,'"+type+"')");
					$("#unfollow-"+type+"-"+id).removeAttr("onclick");
				}
				$("#follow-message-"+type+"-"+id).html(result);
			}
		});
	}
	else	{
		$("#follow-message-"+id).html("Please login to follow");
	}
}

function showComment(ansid)	{	
	var query1 = $("#comment-front-"+ansid).is(':visible');
	if(query1)	{
		$("#comment-front-"+ansid).hide();
		$("#comment-link-"+ansid).text("View comments");
	}
	else	{
		$("#comment-front-"+ansid).show();
		$("#comment-link-"+ansid).text("Hide comments");
	}
}
function addComment(e,ansid,ans_posted_by,qid,qstn_posted_by)	{
	var keyCode = e.keyCode || e.which;
	if(keyCode == 13)	{

		commentInpId="comment-front-ans-"+ansid;
		idRes = "comment-area-front-"+ansid;
		loadTextId = "comment-load-front-text-"+ansid;

		var commentVal = document.getElementById(commentInpId).value;
		
		if((commentVal.trim().length) != 0)	{
			$("#"+commentInpId).attr("disabled", "disabled"); 
			$.ajax(
			{
				type:"post",
				url:"add_comment.php",
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
				}
			}
			)
		}
	}
}

function loadMoreComments(ansid)	{
	var childNodes="",cidList="";
	childNodes=$("#comment-area-front-"+ansid+" .user-comment-sec").length;
	cidList=document.getElementById("cid-front-section-"+ansid).value;
	$.ajax({
		type:"post",
		url:"fetch_comments.php",
		data:
		{
			"flag":2,
			"ansid":ansid,
			"cid_list":cidList,
			"element_count":childNodes
		},
		success:function(res)	{
			if(res==0)	{
				$("#comment-load-front-text-"+ansid).hide();
			}
			else
				$("#cmnt-list-"+ansid).append(res);
		}
	});
}

function showMessageBox(num,divId)	{
	if(num==0)	{
		$("#block-bg").show();
		$("#msg-box-"+divId).show();
	}
	else if(num==1)	{
		$("#msg-box-"+divId).hide();
		$("#block-bg").hide();
	}
}

function sendMessage(root,divId,sender,recp)	{
	msgTxt=document.getElementById("msg-text-"+divId).value;
	if(msgTxt.trim()!="")	{
		$.ajax({
			type:"post",
			url:root+"send_message.php",
			data:
			{
				"sender_id":sender,
				"recp_id":recp,
				"msg-text":msgTxt
			},
			beforeSend:function()	{
				$("#msg-text-"+divId).attr("disabled","disabled");
				$("#load-"+divId).show();
			},
			success:function(res)	{
				$("#load-"+divId).hide();
				$("#main-sec-"+divId).hide();
				$("#btn1-"+divId).hide();
				document.getElementById("msg-text-"+divId).value="";
				$("#msg-text-"+divId).removeAttr("disabled");
				document.getElementById(divId).innerHTML=res;
			}
		});
	}
}
