// CODE FOR POPUP WINDOWS
function launch(newURL, newName, newFeatures, orgName)
{
	var remote = open(newURL, newName, newFeatures);
	if (remote.opener == null)
	remote.opener = window;
	remote.opener.name = orgName;
	return remote;
}

function popup(url, name, width, height)
{
	mywinpos = findscreencenter(width,height);
	settings=
	"toolbar=no,location=no,directories=no,"+
	"status=yes,menubar=no,scrollbars=yes,"+
	"resizable=no,width="+width+",height="+height+",top="+mywinpos[0]+",left="+mywinpos[1];
	orgname="mymain";

	MyNewWindow=launch(url,name,settings,orgname);
}

function findscreencenter( pwinwidth, pwinheight )
{
	winpos = new Array(2);
	scrheight = screen.height;
	scrwidth = screen.width;
	wintop = (scrheight - pwinheight-100) / 2;
	winleft = (scrwidth - pwinwidth) / 2;
	winpos[0] = wintop;
	winpos[1] = winleft;
	return winpos;
}
