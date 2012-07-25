function makeContents(id,src) {
	var jqSrc = $("#"+id ,src);
	var mirrors = jqSrc.children("mirror");
	var output = $("<div></div>");
	for ( var i = 0; i < mirrors.length; i++) {
		//output.append(mirrors[i].text());
		//alert(mirrors[i]);
		
		output.append("<b><font	color='#2046B8'>"+$(mirrors[i]).children("word").text()+"</font></b> ("+(i+1)+")	-	"+$(mirrors[i]).children("pos").text()+".	-	"+$(mirrors[i]).children("mean").text()+"<br>");
		if($(mirrors[i]).children("syn").text() != "")output.append("&nbsp;&nbsp;	<i><font color='#95A3D2'>Syn.	:: </font></i>"+$(mirrors[i]).children("syn").text()+"<br>");
		if($(mirrors[i]).children("ant").text() != "")output.append("&nbsp;&nbsp; <i><font	color='#95A3D2'>Ant. ::	</font></i>"+$(mirrors[i]).children("ant").text()+"<br>");
		if($(mirrors[i]).children("ex").text() != "")output.append("&nbsp;&nbsp; <i><font	color='#95A3D2'>Example	:: </font></i>"+$(mirrors[i]).children("ex").text()+"<br>");
	}
	if(mirrors.length == 0){
		output.append("<font color='#FF0000'>ไม่มีคำแปลในฐานข้อมูล</font>");
	}
	return output;
}