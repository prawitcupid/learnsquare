// Math dialog
//edit by Watee wichiennit 03/01/2008
function SpawMathDialog()
{
}
SpawMathDialog.init = function() 
{
  var aProps = spawArguments;
  document.getElementById("prop_table").style.height = document.getElementById("prop_table").offsetHeight + 'px'; 
  if (aProps && (aProps.href || aProps.name))
  {
    // set attribute values
    if (aProps.href) {
      document.getElementById("chref").value = spawEditor.getStrippedAbsoluteUrl(aProps.href, false);
    }
    if (aProps.name) {
      document.getElementById("cname").value = aProps.name;
    }

    SpawMathDialog.setTarget(aProps.target);
    
    if (aProps.title) {
      document.getElementById("ctitle").value = aProps.title;
    }
  }

  var found = SpawMathDialog.setAnchors(aProps?aProps.href:'');
  var atype = "link";
  if (aProps)
  {
    if (aProps.name)
    {
      atype = "anchor";	
    }
    else if (found)
    {
      atype = "link2anchor";
    }
  }
  if (document.getElementById("canchor").options.length<=1)
  {
    // no anchors found, disable link to anchor feature
    document.getElementById("catype").remove(2);
  }
  SpawMathDialog.changeType(atype);    
}

SpawMathDialog.okClick = function() {

	var pdoc = spawEditor.getActivePageDoc();
	var iProps = spawArguments;
	iProps = pdoc.createElement("<div>");
	iProps.innerHTML = (document.getElementById('equationview').innerHTML);
    if (spawArgs.callback)
    {
      eval('window.opener.'+spawArgs.callback + '(spawEditor, iProps, spawArgs.tbi, spawArgs.sender)');
    }
    window.close();
  //}
}

SpawMathDialog.cancelClick = function() {
  window.close();
}

SpawMathDialog.setTarget = function(target)
{
  for (i=0; i<document.getElementById("ctarget").options.length; i++)  
  {
    tg = document.getElementById("ctarget").options.item(i);
    if (tg.value == target.toLowerCase()) {
      document.getElementById("ctarget").selectedIndex = tg.index;
    }
  }
}

SpawMathDialog.setAnchors = function(anchor)
{
	var found = false;
	var anchors = spawEditor.getAnchors();
	for(var i=0; i<anchors.length; i++)
  {
    var opt = document.createElement("OPTION");
    document.getElementById("canchor").options.add(opt);
    opt.appendChild(document.createTextNode(anchors[i].name));
    opt.value = '#'+anchors[i].name;
    if (opt.value == anchor)
    {
      opt.selected = true;
      found = true;
    }
  }
  return found;
}

SpawMathDialog.changeType = function(new_type)
{
  document.getElementById("catype").selectedIndex = 0;
  if (new_type == "anchor")
  {
    document.getElementById("catype").selectedIndex = 1;
  }
  else if (new_type == "link2anchor")
  {
    document.getElementById("catype").selectedIndex = 2;
  }

  document.getElementById("url_row").style.display = new_type=="link"?"":"none";
	document.getElementById("name_row").style.display = new_type=="anchor"?"":"none";
	document.getElementById("anchor_row").style.display = new_type=="link2anchor"?"":"none";
	document.getElementById("target_row").style.display = (new_type=="link"||new_type=="link2anchor")?"":"none";
	
  //SpawDialog.resizeDialogToContent();
}

if (document.attachEvent)
{
  // ie
  window.attachEvent("onload", new Function("SpawMathDialog.init();"));
}
else
{
  window.addEventListener("load", new Function("SpawMathDialog.init();"), false);
}

