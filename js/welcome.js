 $(document).on("click","#user-section",function()	{
	$("#userdetl-section").show();
}); 

$(document).ready(function()	{
 $("body").click(function()	{
	$("#userdetl-section").hide();
 });
 
 $("#matched-1").css("border-bottom","1px solid #fff");
 $("#matched-1").css("border-top","3px solid #191970");
 
 $("#matched-1").click(function()	{
	/* $("#matched-1").css("border-top","1px solid black"); */
	$("#matched-1").css("border-bottom","1px solid #fff");
	$("#matched-1").css("border-top","3px solid #191970");
	$("#exprmt-2").css("border-top","1px solid #C4C4C4");
	$("#theory-3").css("border-top","1px solid #C4C4C4");
	$("#analyse-4").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-bottom","1px solid #C4C4C4");
	$("#theory-3").css("border-bottom","1px solid #C4C4C4");
	$("#analyse-4").css("border-bottom","1px solid #C4C4C4");
	
 });
  $("#exprmt-2").click(function()	{
	$("#matched-1").css("border-bottom","1px solid #C4C4C4");
	$("#matched-1").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-top","3px solid #191970");
	$("#theory-3").css("border-top","1px solid #C4C4C4");
	$("#analyse-4").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-bottom","1px solid #fff");
	$("#theory-3").css("border-bottom","1px solid #C4C4C4");
	$("#analyse-4").css("border-bottom","1px solid #C4C4C4");
 });
  $("#theory-3").click(function()	{
	$("#matched-1").css("border-bottom","1px solid #C4C4C4");
	$("#matched-1").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-top","1px solid #C4C4C4");
	$("#theory-3").css("border-top","3px solid #191970");
	$("#analyse-4").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-bottom","1px solid #C4C4C4");
	$("#theory-3").css("border-bottom","1px solid #fff");
	$("#analyse-4").css("border-bottom","1px solid #C4C4C4");
	
 });
  $("#analyse-4").click(function()	{
	$("#matched-1").css("border-bottom","1px solid #C4C4C4");
	$("#matched-1").css("border-top","1px solid #C4C4C4");
	$("#exprmt-2").css("border-top","1px solid #C4C4C4");
	$("#theory-3").css("border-top","1px solid #C4C4C4");
	$("#analyse-4").css("border-top","3px solid #191970");
	$("#exprmt-2").css("border-bottom","1px solid #C4C4C4");
	$("#theory-3").css("border-bottom","1px solid #C4C4C4");
	$("#analyse-4").css("border-bottom","1px solid #fff");
	
 });
 
}); 
function showQstn(flag)	{
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
			document.getElementById('qstn-res').innerHTML = this.responseText;
		}
	};
	xmlhttp.open("GET","load_qstn_home.php?flag="+flag,true);
	xmlhttp.send();
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
