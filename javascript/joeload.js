	 function JoeLoading()
	 {
		alert('ok');
	 }	
	function JoeLoad()
	 {
		$('chatbox').scrollTop = $('chatbox').scrollHeight;
		setTimeout('JoeLoading',5000);
	 }
	 function JoeSending()
	 {
		text = $('joe_text').value;
		var yreq = {  
			onCreate: function(){
			$('joe_text').disabled = true;
			$('joe_send').disabled = true;
			//$('joe_text').value = '';
		  }
		}; 
		Ajax.Responders.register(yreq); 	
		args='joe_text='+encodeURIComponent(text)+'&jae_random='+Math.random();
		var do_ajax=new Ajax.Request('index.php?mod=Courses&op=joe_jae_send&cid=1&sid=1',{method:'post',parameters:args, onComplete:handle_response});
		Ajax.Responders.unregister(yreq);	
	 }
	 function JoeDelete()
	 {
		$('chatbox').innerHTML = '<font color="#ff0000"><?php echo _JORJAE_SAY_DELETE; ?></font>';
	 }
	function handle_response(request)
	{
		var response=request.responseText;	
		//alert(response);
		$('chatbox').innerHTML +=  '<div>'+response+'</div>';
		$('joe_send').disabled = false;
		$('joe_text').disabled = false;
		$('joe_text').value = '';
		$('joe_text').focus();
	}
	 window.onload=JoeLoad;