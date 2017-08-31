function testFunc()	{
	var t="#para";
	$.ajax({
		url:"testdata.txt",
		success:function(result)	{
			$(t).html(result);
		}
	});
}