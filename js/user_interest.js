function chooseInterest(sec,tagId)	{
	
	if(sec==1)
		idVal="qstn-int-"+tagId;
	else if(sec==2)
		idVal="user-int-"+tagId;
	else 
		idVal="topic-"+tagId;
	var checkElement = parseInt($("#"+idVal).attr("data-set"));
	if(checkElement==0)	{
		$("#"+idVal).addClass("user-choice-sel");
		$("#"+idVal).attr("data-set","1");
	}
	else if(checkElement==1)	{
		$("#"+idVal).removeClass("user-choice-sel");
		$("#"+idVal).attr("data-set","0");
	}
}

function processInterests()	{
	var lenFeature = $("#feature-tags .user-choice-sel").length;
	var lenUser = $("#user-tags .user-choice-sel").length;
	var lenTopic1 = $("#topic-keys-1 .user-choice-sel").length;
	var lenTopic2 = $("#topic-keys-2 .user-choice-sel").length;
	var lenTopic3 = $("#topic-keys-3 .user-choice-sel").length;
	var lenTopic4 = $("#topic-keys-4 .user-choice-sel").length;
	var totLen = lenFeature+lenUser+lenTopic1+lenTopic2+lenTopic3+lenTopic4;
	var intVal="";
	for(var i=0; i<totLen; i++)	{
		intVal=document.getElementsByClassName("user-choice-sel")[i].innerHTML;
		if(i==0)	{
			document.getElementById("res-int-list").value+=intVal;
		}
		else	{
			document.getElementById("res-int-list").value+=", "+intVal;
		}
	}
}








