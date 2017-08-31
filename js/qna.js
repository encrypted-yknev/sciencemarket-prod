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
function addComment(ansid,textVal,id,ans_posted_by,qid,qstn_posted_by)	{
	var id_res="#"+id;
	if((textVal.trim().length) != 0)	{
		$(id_res).attr("disabled", "disabled"); 
		$.ajax(
		{
			type:"post",
			url:"add_comment.php",
			data:
				{
					"ansid":ansid,
					"text":textVal,
					"posted_by":ans_posted_by,
					"qid":qid,
					"q_posted_by":qstn_posted_by
				},
			beforeSend:function()	{
				$("#load-msg-"+ansid).text("Please wait...");
			},
			success:function(result)	{
				$("#load-msg-"+ansid).text("");
				$(id_res).html(result);
				document.getElementById("comment-"+ansid).value="";
				$(id_res).removeAttr("disabled");
			}
		}
		)
	}
}


function showComment(id)	{
	var id_res="#"+id;
	$(id_res).slideToggle();
}
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

function loadAnswerList(ans,qid,postedBy,page)	{
	
	$.ajax({
		type:"post",
		url:"load_answers.php",
		data:{
			"ans":ans,
			"qid":qid,
			"postedBy":postedBy,
			"page":page
		},
		beforeSend:function()	{
			$("#block-container").show();
			$("#load-section").show();
			$(".msg-section").hide();
		},
		success:function(result)	{
			$("#user-ans").hide();
			$("#ans_container").html(result);
		},
		complete:function()	{
			$("#block-container").hide();
			$("#load-section").hide();
			$("#user-ans").val("");
		}
		
	});
}

