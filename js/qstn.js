$(document).ready(function()	{
	//alert("hi");
	$("#main-dashboard").load("post_qstn.php?qtopic=&qtitl=&qdesc=&page=load&tags=");
	var focusOutCnt=0;
	
	$("#tag input").on("focusout",function()	{
		var x=$.trim(this.value);
		if(x) {
			focusOutCnt++;
			$(".alert-msg-section").text(""); 
			if(focusOutCnt==4)	{
				$("#alert-msg").html("<span class='alert-msg-section'>No more tags allowed!</span>");
				$(".q-tags").attr("disabled","disabled");
			}
				
			$("#tag-res").append('<span class="tag-name">'+x+'</span>');
			$("#tag-value").append(x+" ");
			$(this).focus();
		}
		else	{
			if(focusOutCnt==0)
				$("#alert-msg").html("<span class='alert-msg-section'>Select at-least one tag</span>");
				
		}
		this.value = ""; 
	//	$(this).focus();
	});
	
	
	$(document).on("click",".tag-name",function()	{
		$(this).remove();
		focusOutCnt--;
		if(focusOutCnt < 4)	{
			$(".alert-msg-section").text("");
			$(".q-tags").removeAttr("disabled");
		}
			
	});
	
	$(document).on("click","#menu-bar",function()	{
		/* $("#side-bar").css({"width":"80%","position":"relative","margin-left":"2%"});
		//$("#menu-bar").css({"margin-left":"none"});
		$("#main-container").css({"margin-left":"","position":"relative","width":"100%"}); */
		$("#side-bar").toggle("fade");
	});
	var width = $(window).width();
    if (width > 900) {
        $("#side-bar").show();
    }
	$("#nav-id").click(function(e)	{
		$("#options-menu").show('slide', {direction: 'left'}, 500);
		$("#block").show();
		e.stopPropagation();
	});
	$("#block").click(function()	{
		$("#options-menu").hide('slide', {direction: 'left'}, 500);
		$("#block").hide();
	});
	
	$('form input').on('keypress', function(e) {
		return e.which !== 13;
	});
	
	$('.q-tags').on('keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if(keyCode == 13)	{
			$(this).blur();
		}
	});
	
});


	
function getTagsName()	{
	var x=document.getElementById("tag-res").childElementCount;
	var counter,tag="";
	for(counter=0; counter<x; counter++)	{
		tag+=document.getElementsByClassName("tag-name")[counter].innerText+" ";
	}
	document.getElementById("tags").value=tag;
	//return tag;
}
function increaseCount(id,qid,userid,func)	{
	if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} 
	else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById(id).innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET","update_votes1.php?id="+qid+"&userid="+userid+"&func="+func+"&qaflag=0",true);
	xmlhttp.send();
}

function demo(tags)	{
	alert(tags);
}
/* function postQuestion(topic,title,desc,page,tags)	{
	if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} 
	else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('main-dashboard').innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET","post_qstn.php?qtopic="+topic+"&qtitl="+title+"&qdesc="+desc+"&page="+page+"&tags="+tags,true);
	xmlhttp.send();
} */

function postQuestion(topic,title,desc,page,tags)	{
	$.ajax(
		{
			type:"post",
			url:"post_qstn.php",
			data:
			{
				"qtopic":topic,
				"qtitl":title,
				"qdesc":desc,
				"page":page,
				"tags":tags
			},
			beforeSend:function()	{
				$("#block-container").show();
				$("#load-section").show();
			},
			success:function(result)	{
				if(result=='1')
					window.location.href="qa_forum.php";
				else	{
					$("#block-container").hide();
					$("#load-section").hide();
					$("#qstn-info").html(result);
				}
			}
		}
	);
}
function getSubTopics(topic_name)	{
	$.ajax(
		{
			type:"post",
			url:"get_sub_topics.php",
			data:
			{
				"topic":topic_name
			},
			success:function(result)	{
				$("#q-sub-topic").html(result);
			}
		}
	);
}

function showQstnResults(srchText)	{
	$.ajax(
	{
		type:"post",
		url:"show_qstns.php",
		data:{"text":srchText},
		success:function(result)	{
			$("#qstn-list").html(result);
		}
	}
	);
}

function loadTagNames(srchText)	{
	if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} 
	else {
		// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById('tag-res').innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET","load_tag_names.php?text="+srchText,true);
	xmlhttp.send();
}

function populateText(txt)	{
	document.getElementById("q-tags").value="";
	document.getElementById("q-tags").value=txt;
	document.getElementById("q-tags").focus();
}

function getInputInfo(x)	{
	if(x==1)	
		document.getElementById("qstn-info").innerHTML="<div class='alert alert-info'>Choose the topics from the list</div>";
	else if(x==2)
		document.getElementById("qstn-info").innerHTML="<div class='alert alert-info'>Choose the sub-topic from the list</div>";
	else if(x==3)
		document.getElementById("qstn-info").innerHTML="<div class='alert alert-info'>Enter a brief title on what you question is all about. Before posting any question check if your question has already been posted and has been answered. If so, try to follow up with that question. Avoid posting repeated question.</div>";
	else if(x==4)
		document.getElementById("qstn-info").innerHTML="<div class='alert alert-info'>Explain your question in plain and simple words such that users do not get confused.</div>";
	else if(x==5)
		document.getElementById("qstn-info").innerHTML="<div class='alert alert-info'>Tags help users to find what type of question it is and what are the keywords being used. We also use tags to associate users interest with that topic/question. Please select at-least one tag and at-most 4 tags per question.</br>1. Avoid using # as prefix in the tag names.</br>2. Avoid using spaces. Better to use '_ (underscore)' or '- (hyphen)' to split between words.</br>3. Try to keep the names short and simple.</div>";
}

function showSubTopics(id)	{
	var id_res="#"+id;
	$(id_res).slideToggle();
}