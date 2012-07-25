var spendTime = 0;
var startTime = null;
var url = "xestat.php";
window.onfocus = function () {
	startTime = new Date();	
};
window.onblur = function () {
	now = new Date();
	spendTime += now - startTime;
	startTime = null;
};
window.onbeforeunload = function () {	
	var xmlhttp;        
    if (window.XMLHttpRequest)
    {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    }
    else        //If it's a bad browser...
    {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    if (startTime != null){
    	now = new Date();
    	spendTime += now - startTime;
    }
    var params = "time="+Number(spendTime/1000)+"&referrer="+escape(document.referrer);
    xmlhttp.open("POST",url,false);        //The false at the end tells ajax to use a synchronous call which wont be severed by the user leaving.
	xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xmlhttp.send(params);        //Send the request and don't wait for a response.
};