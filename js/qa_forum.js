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
				document.getElementById("ans-"+qid).value="";
				if(flag == 0)
					document.getElementById("toggle-ans-sec-"+qid).innerHTML=res;
				else if(flag == 1)	
					document.getElementById("toggle-top-ans-sec-"+qid).innerHTML=res;
			}
		});
	}
}
function toggleAns(qid,x)	{
	if(x == 0)	{
		var query1 = $("#toggle-ans-sec-"+qid).is(':visible');
		var query2 = $("#toggle-top-ans-sec-"+qid).is(':visible');
		if(query1 == true)	{
			$("#toggle-ans-sec-"+qid).slideUp();
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
			$("#toggle-top-ans-sec-"+qid).slideUp();
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
	var sectionId;
	if(func=="0")	{
		if(flag == 0)
			sectionId="#up-vote-ans-"+id;
		else if(flag == 1)
			sectionId="#up-vote-top-ans-"+id;
		var upVoteVal = parseInt($(sectionId).text());
	}
	else if(func=="1")	{
		if(flag == 0)
			sectionId="#down-vote-ans-"+id;
		else if(flag == 1)
			sectionId="#down-vote-top-ans-"+id;
		var downVoteVal = parseInt($(sectionId).text());
	}
	
	if(func=="0")	{
		if(voteCheckCount > 0)	{
			if(flag==0)	{
				$("#glyph-up-ans-"+id).removeClass("glyph-ans-upvoted");
				$("#upvote-value-ans-"+id).val("0");
			}
			else if(flag==1)	{
				$("#glyph-up-top-ans-"+id).removeClass("glyph-ans-upvoted");
				$("#upvote-value-top-ans-"+id).val("0");
			}
			requestType="D";
			
			$(sectionId).text(upVoteVal-1);
		}
		else	{
			if(flag==0)	{
				$("#glyph-up-ans-"+id).addClass("glyph-ans-upvoted");
				$("#upvote-value-ans-"+id).val("1");
			}
			else if(flag==1)	{
				$("#glyph-up-top-ans-"+id).addClass("glyph-ans-upvoted");
				$("#upvote-value-top-ans-"+id).val("1");
			}
			requestType="A";
			$(sectionId).text(upVoteVal+1);
		}
	}
	else if(func=="1")	{
		if(voteCheckCount > 0)	{
			if(flag==0)	{
				$("#glyph-down-ans-"+id).removeClass("glyph-ans-downvoted");
				$("#downvote-value-ans-"+id).val("0");
			}
			else if(flag==1)	{
				$("#glyph-down-top-ans-"+id).removeClass("glyph-ans-downvoted");
				$("#downvote-value-top-ans-"+id).val("0");
			}
			requestType="D";
			$(sectionId).text(downVoteVal-1);
		}
		else	{
			if(flag==0)	{
				$("#glyph-down-ans-"+id).addClass("glyph-ans-downvoted");
				$("#downvote-value-ans-"+id).val("1");
			}
			else if(flag==1)	{
				$("#glyph-down-top-ans-"+id).addClass("glyph-ans-downvoted");
				$("#downvote-value-top-ans-"+id).val("1");
			}
			requestType="A";
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
			"qaflag":"1"
		}
		/* success:function(result)	{
			$(sectionId).html(result);
		} */
	});
}
