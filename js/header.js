/* $(document).on("click","#user-icon-section",function()	{
	$("#profile-section").show();
});

$(document).on("click","#more-link",function()	{
	$("#head-nav-menu").show();
});  */

$(document).ready(function()	{
	$("body").click(function()	{
		$("#profile-section").hide();
		$("#head-nav-menu").hide();
		$("#srch-result").hide();
		$("#srch-result-mobile").hide();
	});
	$(document).on("click","#user-icon-section",function()	{
		$("#profile-section").show();
	});

	$(document).on("click","#more-link",function()	{
		$("#head-nav-menu").show();
	}); 
});

 function fetchQuestions(txt,slashes)	{
	$("#srch-result").show();
	$.ajax({
		type:"post",
		url:slashes+"search_questions.php",
		data:
		{
			"val":txt,
			"loc":slashes,
		},
		success:function(result)	{
			$("#srch-result").html(result);
		}
	});
} 
function fetchQuestionsMobile(txt,slashes)	{
	$("#srch-result-mobile").show();
	$.ajax({
		type:"post",
		url:slashes+"search_questions.php",
		data:
		{
			"val":txt,
			"loc":slashes,
		},
		success:function(result)	{
			$("#srch-result-mobile").html(result);
		}
	});
}
